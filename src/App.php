<?php declare(strict_types = 1);

namespace Glu;

use Glu\Adapter\DataSource\DbalSource;
use Glu\Adapter\Templating\TwigTemplateRenderer;
use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\ServiceDefinition;
use Glu\Event\EventDispatcher;
use Glu\Event\Lifecycle\ControllerExecutedEvent;
use Glu\Event\Lifecycle\ExceptionThrownEvent;
use Glu\Event\Lifecycle\RequestReceivedEvent;
use Glu\Event\Lifecycle\ResponseReadyEvent;
use Glu\Event\Lifecycle\RouteMatchedEvent;
use Glu\Event\Listener;
use Glu\Extension\Extension;
use Glu\Http\Factory;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;
use Glu\Routing\Router;
use Glu\Templating\TemplateRenderer;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class App implements AppInterface
{
    private readonly Factory $httpFactory;
    private ?Request $request;

    private readonly Router $router;
    private readonly TemplateRenderer $templateRenderer;
    private array $defaultHeaders;
    private Container $container;

    private array $errorHandlers;

    private LoggerInterface $logger;
    private EventDispatcher $eventDispatcher;

    private ?CacheItemPoolInterface $cache;

    private float $startTime;
    private float $endTime;

    public function __construct(
        array          $sources = [],
        array          $services = [],
        /** @var Extension[] $extensions */
        array          $extensions = [],
        array $listeners = [],
        ?LoggerInterface $logger = null,
        string $templatesDir = __DIR__ . '/../../../../template',
        ?string $cacheDir = null
    )
    {
        $this->startTime = \microtime(true);
        $this->request = null;
        $this->logger = $logger ?? new NullLogger();
        $this->router = new Router();

        $this->container = new Container($services, [
            'data_directory' => __DIR__ . '/../../../../var/data/'
        ]);

        $this->defaultHeaders = [
            'content-type' => 'text/html; charset=UTF-8',
            'cache-control' => 'private'
        ];

        foreach ($sources as $name => $dsn) {
            $this->container->add(new ServiceDefinition(
                'source_' . $name,
                DbalSource::class,
                [$dsn],
                true,
                'create'
            ));
        }

        $templatesDirs = [
            $templatesDir
        ];

        $this->templateRenderer = new TwigTemplateRenderer(
            $templatesDirs,
            $this->router,
            $cacheDir
        );

        $this->container->addSynthetic('template_renderer', $this->templateRenderer);

        // event dispatcher
        $my = [];
        foreach ($listeners as $eventName => $listener) {
            if (\is_array($listener)) {
                foreach ($listener as $item) {
                    $my[] = new Listener($eventName, $item);
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
            return $this->httpFactory->createResponse(404);
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

            return new Response('', 500);
        }

        $responseReadyEvent = new ResponseReadyEvent($this->request, $response);
        $this->eventDispatcher->dispatch($responseReadyEvent);

        $response = $responseReadyEvent->response();

        if ($request->method() === 'head') {
            $response->contents = '';
        }

        $this->endTime = \microtime(true);

        return $response;
    }

    public function send(Response $response): void
    {
        $headers = $response->headers();
        foreach ($headers as $headerName => $headerValue) {
            header($headerName . ': ' . $headerValue);
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
        return $this->templateRenderer->render($path, $this->request, $context);
    }

    private function loadExtensions(array $extensions): void {
        foreach ($extensions as $extensionFqn => $extensionContext) {
            /** @var Extension $extension */
            $extension = $extensionFqn::load($this->container, $extensionContext);

            foreach ($extension->listeners() as $listener) {
                $this->eventDispatcher->register($listener);
            }

            foreach ($extension->templateDirectories() as $templateDirectory) {
                $this->templateRenderer->registerDirectory($templateDirectory);
            }
            foreach ($extension->rendererFunctions() as $_function) {
                $this->templateRenderer->registerFunction($_function);
            }
            foreach ($extension->dataSources() as $name => $dsn) {
                // make it lazy
                //$this->sources[$name] = DbalSource::create($dsn);
            }
            foreach ($extension->routes() as $route) {
                $this->router->add($route);
            }
        }
    }

    public function addDefaultHeader(string $name, string $value): void
    {
        $this->defaultHeaders[\mb_strtolower($name)] = $value;
    }
}
