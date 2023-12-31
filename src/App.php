<?php declare(strict_types = 1);

namespace Glu;

use Glu\Adapter\DataSource\DbalSource;
use Glu\Adapter\DependencyInjection\Symfony\CompilerPass\ListenerCompilerPass;
use Glu\Adapter\DependencyInjection\Symfony\CompilerPass\SourceCompilerPass;
use Glu\Adapter\DependencyInjection\Symfony\CompilerPass\TemplatingEngineCompilerPass;
use Glu\DataSource\Source;
use Glu\DataSource\SourceFactoryFactory;
use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\Reference;
use Glu\DependencyInjection\Service;
use Glu\Event\EventDispatcher;
use Glu\Event\Lifecycle\ControllerExecutedEvent;
use Glu\Event\Lifecycle\ExceptionThrownEvent;
use Glu\Event\Lifecycle\RequestReceivedEvent;
use Glu\Event\Lifecycle\ResponseReadyEvent;
use Glu\Event\Lifecycle\RouteMatchedEvent;
use Glu\Event\ListenerImp;
use Glu\Extension\Extension;
use Glu\Extension\Twig\Templating\TwigEngine;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;
use Glu\Routing\Router;
use Glu\Templating\Renderer;
use Glu\Templating\Engine;
use Glu\Templating\RendererFactory;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use function microtime;

final class App implements AppInterface
{
    private ?Request $request;

    private readonly Router $router;
    private readonly Renderer $engineResolver;
    private array $defaultHeaders;
    private Container $container;

    private array $errorHandlers;

    private LoggerInterface $logger;
    private EventDispatcher $eventDispatcher;

    private ?CacheItemPoolInterface $cache;

    private float $startTime;
    private float $endTime;

    private ContainerBuilder $containerBuilder;

    public function __construct(
        string $appDir,
        array          $sources = [],
        array          $services = [],
        /** @var Extension[] $extensions */
        array          $extensions = [],
        array $listeners = [],
        ?LoggerInterface $logger = null,
        string $templatesDir = __DIR__ . '/../../../../template',
        array $parameters = []
    )
    {
        $this->startTime = microtime(true);

        $this->containerBuilder = new ContainerBuilder(
            new ParameterBag(
                array_merge(
                    [
                        'glu.app_dir' => $appDir,
                        'glu.data_directory' => $appDir . '/var/data/',
                        'glu.cache_dir' => $appDir . '/var/cache/',
                        'glu.templating.engines' => [],
                        'glu.templating.directories' => [
                            $appDir . '/template'
                        ],
                        'glu.templating.functions' => [],
                    ],
                    $parameters
                )
            ));

        //$environment = new Environment($appDir);
        $this->containerBuilder->register('glu.environment', Environment::class)
            ->setArguments(['%glu.app_dir%']);

        $this->request = null;
        $this->logger = $logger ?? new NullLogger();
        $this->router = new Router();

        $this->container = new Container($services, [
            'data_directory' => $appDir . '/var/data/',
            'glu.cache_dir' => $appDir . '/var/cache/'
        ]);

        $this->defaultHeaders = [
            'content-type' => 'text/html; charset=UTF-8',
            'cache-control' => 'private'
        ];

        $this->containerBuilder->register(
            Container::SERVICE_DATA_SOURCE_FACTORY,
            SourceFactoryFactory::class,
        )->setArgument('$sourceFactories', []);
        $this->containerBuilder->setParameter('glu.sources', $sources);

        $templatesDirs = [
            $templatesDir
        ];

        $this->containerBuilder->register('glu.templating.renderer_factory', RendererFactory::class)
            ->setArgument('$engines', [])
            ->setPublic(true);

        $this->containerBuilder->register('glu.templating.renderer', Renderer::class)
            ->setFactory([new Reference('glu.templating.renderer_factory'), 'create'])
            ->setPublic(true);

        //$this->containerBuilder->register('glu.router', Router::class);
        $this->containerBuilder->set('glu.router', $this->router);

        $this->containerBuilder->addCompilerPass(new SourceCompilerPass());
        $this->containerBuilder->addCompilerPass(new ListenerCompilerPass());
        $this->containerBuilder->addCompilerPass(new TemplatingEngineCompilerPass());



        /*$this->container->set(new ServiceDefinition(
            'glu.templating.renderer',
            RendererFactory::class,
            [
                'glu.templating.engines'
            ],
            [],
            true,
            'create'
        ));
        $this->container->setSynthetic('glu.router', $this->router);*/

        // event dispatcher
        $my = [];
        foreach ($listeners as $eventName => $listener) {
            if (\is_array($listener)) {
                foreach ($listener as $item) {
                    $my[] = new ListenerImp($eventName, $item);
                }
            } else {
                $my[] = $listener;
            }
        }
        $this->eventDispatcher = new EventDispatcher($my, $this->container);

        $this->loadExtensions($extensions);

        $this->errorHandlers = [
            500 => function(\Throwable $e) {
                return new Response(
                    'An error occurred:<br/>' . $e->getMessage() . '<br/>',
                    500
                );
            }
        ];

        $this->containerBuilder->compile();
    }

    public function handle(Request $request): Response
    {
        $this->request = $request;

        $requestReceivedEvent = new RequestReceivedEvent($request);
        $this->eventDispatcher->dispatch($requestReceivedEvent);
        if ($requestReceivedEvent->responseHasBeenSet()) {
            return $requestReceivedEvent->response();
        }

        $matchResult = $this->router->match($request);
        $routeMatchedEvent = new RouteMatchedEvent($request, $matchResult);
        $this->eventDispatcher->dispatch($routeMatchedEvent);
        if ($routeMatchedEvent->responseHasBeenSet()) {
            return $routeMatchedEvent->response();
        }
        $matchResult = $routeMatchedEvent->matchResult();

        if ($matchResult->isFound() === false) {
            return new Response('', 404);
        }

        // security layer
        /*if ($matchResult->secured !== null && SessionManagement::loggedInRole() !== $matchResult->secured) {
            $this->response = $this->httpFactory->createResponse(401);
        }*/
        // END security layer

        $controller = $matchResult->route()->controller();
        if (is_string($controller)) {
            $controller = $this->container->get($controller);
        }

        $response = new Response('', 200, $this->defaultHeaders);
        try {
            $controller->call(
                $this,
                $request,
                $response,
                $matchResult->parameters()
            );

            $controllerExecutedEvent = new ControllerExecutedEvent(
                $this->request,
                $response
            );
            $this->eventDispatcher->dispatch($controllerExecutedEvent);
            if ($controllerExecutedEvent->responseHasBeenSet()) {
                return $controllerExecutedEvent->response();
            }
        } catch (\Throwable $e) {
            $exceptionThrownEvent = new ExceptionThrownEvent($this->request, $response, $e);
            $this->eventDispatcher->dispatch($exceptionThrownEvent);
            if ($exceptionThrownEvent->responseHasBeenSet()) {
                return $exceptionThrownEvent->response();
            }
            \var_dump($e->getFile(), $e->getLine(), \get_class($e), $e->getTraceAsString());
            return new Response($e->getMessage(), 500);
        }

        $responseReadyEvent = new ResponseReadyEvent($this->request, $response);
        $this->eventDispatcher->dispatch($responseReadyEvent);

        $response = $responseReadyEvent->response();

        if ($request->method() === 'head') {
            $response->contents = '';
        }

        $this->endTime = microtime(true);

        return $response;
    }

    public function send(Response $response): void
    {
        http_response_code($response->statusCode());
        $headers = $response->headers();
        foreach ($headers as $headerName => $headerValue) {
            header($headerName . ': ' . implode(', ', $headerValue));
        }
        echo $response->contents();
    }

    public function get(string $path, callable $callback, ?string $name = null): void
    {
        $this->addPath('GET', $path, $callback, $name);
    }

    public function addPath(
        string $method,
        string $path,
        callable|string $callback,
        ?string $name,
        ?string $secured = null
    )
    {
        $method = \mb_strtolower($method);
        if ($name === null) {
            $name = $method . '_' . $path;
        }
        $this->router->add(
            new Route($name, $method, $path, $callback)
        );
    }

    public function addRedirect(string $from, string $to, int $code = 302) {
        $this->router->add(
            new Route('redirect_', 'get', $from, function () use ($to, $code) {
                return new Response('', $code, [
                    'location' => $to
                ]);
            }));
    }

    public function render(string $path, array $context = []): string
    {
        /** @var Renderer $renderer */
        $renderer = $this->containerBuilder->get('glu.templating.renderer_factory');
        return $renderer->render($path, $this->request, $context);
    }

    /**
     * @param Extension[] $extensions
     */
    private function loadExtensions(array $extensions): void {
        $templatingDirectories = $this->containerBuilder->getParameter('glu.templating.directories');
        $templatingFunctions = $this->containerBuilder->getParameter('glu.templating.functions');

        foreach ($extensions as $extensionFqn => $extensionContext) {
            $arguments = [];
            foreach ($extensionContext as $id) {
                if ($this->containerBuilder->hasParameter($id)) {
                    $arguments[] = '%' . $id . '%';
                } else {
                    $arguments[] = $id;
                }
            }
            $this->containerBuilder->register($extensionFqn, $extensionFqn)
                ->setArguments(
                    $arguments
                );
        }

        foreach ($extensions as $extensionFqn => $extensionContext) {
            /** @var Extension $extension */
            $extension = $this->containerBuilder->get($extensionFqn);
            //$extension = $this->$extensionFqn::load($this->container, $extensionContext);

            foreach ($extension->containerDefinitions() as $service) {
                if ($service instanceof Service) {
                    $tags = [];
                    foreach ($service->tags() as $tag) {
                        $tags[$tag] = [];
                    }

                    $arguments = [];
                    foreach ($service->arguments() as $argument) {
                        if ($argument instanceof Reference) {
                            $arguments[] = new \Symfony\Component\DependencyInjection\Reference($argument->id());
                        } else {
                            $arguments[] = $argument;
                        }
                    }

                    $this->containerBuilder
                        ->register($service->id(), $service->fqn())
                        ->setArguments($arguments)
                        ->setPublic(true)
                        ->setTags(
                            $tags
                        );
                } else {
                    $this->containerBuilder->setParameter($service->id(), $service->value());
                }

            }

            foreach ($extension->listeners() as $listener) {
                $this->eventDispatcher->register($listener);
            }

            foreach ($extension->configuration() as $templateDirectory) {
                $templatingDirectories[] = $templateDirectory;
            }

            foreach ($extension->dataSources() as $name => $dsn) {
                // make it lazy
                //$this->sources[$name] = DbalSource::create($dsn);
            }
            foreach ($extension->routes() as $route) {
                $this->router->add($route);
            }
        }

        $this->containerBuilder->setParameter('glu.templating.directories', $templatingDirectories);
        $this->containerBuilder->setParameter('glu.templating.functions', $templatingFunctions);
    }

    public function addDefaultHeader(string $name, string $value): void
    {
        $this->defaultHeaders[\mb_strtolower($name)] = $value;
    }
}
