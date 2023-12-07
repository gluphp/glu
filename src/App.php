<?php declare(strict_types = 1);

namespace Glu;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Glu\Adapter\DataSource\DbalSource;
use Glu\Adapter\Templating\TwigTemplateRenderer;
use Glu\DataSource\Source;
use Glu\DependencyInjection\Service;
use Glu\DependencyInjection\ServiceLocator;
use Glu\Event\EventDispatcher;
use Glu\Event\Listener;
use Glu\Event\ResponseEvent;
use Glu\Extension\Extension;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Router;
use Glu\Templating\TemplateRenderer;

final class App
{
    private readonly Router $router;
    private readonly TemplateRenderer $templateRenderer;
    private array $defaultHeaders;
    private ServiceLocator $locator;
    /** @var Source[] */
    private array $sources;

    private array $errorHandlers;

    private LoggerInterface $logger;

    private ?Request $currentRequest;

    private $eventDispatcher;

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
        ?string $cacheDir = null,
        CacheItemPoolInterface $cache = null
        //ServiceLocator $locator = null
    )
    {
        $this->startTime = \microtime(true);
        $this->currentRequest = null;
        $this->logger = $logger ?? new NullLogger();
        $this->router = new Router();
        $this->cache = $cache ?? new FilesystemAdapter(
            'tomato',
            0,
            $cacheDir
        );

        $this->defaultHeaders = [
            'content-type' => 'text/html; charset=UTF-8',
            'cache-control' => 'private'
        ];

        $this->sources = [];
        foreach ($sources as $name => $dsn) {
            // make it lazy
            $services[] = new Service(
                'source_' . $name,
                DbalSource::class,
                [$dsn],
                true,
                'create'
            );
            //$this->sources[$name] = DbalSource::create($dsn);
        }

        $this->locator = new ServiceLocator($services);

        $templatesDirs = [
            $templatesDir
        ];

        $this->templateRenderer = new TwigTemplateRenderer(
            $templatesDirs,
            $this->router,
            $cacheDir
        );

        $this->locator->addSynthetic('template_renderer', $this->templateRenderer);

        foreach ($extensions as $extensionFqn => $extensionContext) {
            /** @var Extension $extension */
            $extension = $extensionFqn::load($this->locator, $extensionContext);

            foreach ($extension->listeners() as $listener) {
                $listeners[] = $listener;
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
                $this->router->add(
                    $route->name(),
                    $route->methods()[0],
                    $route->path(),
                    $route->controller()
                );
            }
        }

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

        $this->eventDispatcher = new EventDispatcher($my, $this->locator);


        $this->errorHandlers = [
            500 => function(\Throwable $e) {
                return new Response(
                    'An error occurred:<br/>' . $e->getMessage() . '<br/>',
                    500
                );
            }
        ];
    }

    public function run(Request $request)
    {
        $this->currentRequest = $request;
        $cacheKey = (new CacheKeyCalculator())->key($request);

        $cachedResponse = $this->cache->getItem($cacheKey);
        if ($cachedResponse->isHit() && false) {
            $this->sendResponse($cachedResponse->get());
            exit();
        }
        //SessionManagement::start();

        $routeMatch = $this->router->match($request);
        /*$this->eventDispatcher->dispatch(new Event('tomato:route:matched', [
            'match' => $routeMatch
        ]));*/

        if ($routeMatch->found === false) {
            http_response_code(404);
            echo 'Not found';
            exit();
        }

        // security layer
        if ($routeMatch->secured !== null && SessionManagement::loggedInRole() !== $routeMatch->secured) {
            http_response_code(401);
            echo 'Access denied';
            exit();
        }
        // END security layer

        // controller as service
        $controller = $routeMatch->controller;
        if (is_string($controller)) {
            $controller = $this->locator->get($controller);
        }

        try {
            $response = new Response('', 200, []);
            $controller->call(
                $this,
                $request,
                $response,
                $routeMatch->parameters
            );
        } catch (\Throwable $e) {
            /*$this->eventDispatcher->dispatch(new Event('on_error', [
                'exception' => $e,
                'response' => null
            ]));*/
            $response = $this->errorHandlers[500]($e);
        }

        $event = new ResponseEvent('glu.response', $response, $this->currentRequest);
        $this->eventDispatcher->dispatch($event);

        $response = $event->response();

        if ($request->method === 'HEAD') {
            $response->contents = '';
        }

        $this->sendResponse($response);

        $cachedResponse->set($response);
        $this->cache->save($cachedResponse);

        $this->endTime = \microtime(true);

        //echo $this->endTime-$this->startTime;
    }

    private function sendResponse(Response $response): void
    {
        $headers = \array_merge($this->defaultHeaders, $response->headers);
        foreach ($headers as $headerName => $headerValue) {
            header($headerName . ': ' . $headerValue);
        }
        echo $response->contents;
    }

    public function get(string $path, callable $callback, ?string $name = null): void
    {
        $this->path('GET', $path, $callback, $name);
    }

    public function getStatic(string $path, string $callback, ?string $name = null): void
    {
        $this->path('GET', $path, $callback, $name);
    }

    public function path(
        string $method,
        string $path,
        callable|string $callback,
        ?string $name,
        ?string $secured = null
    )
    {
        if ($name === null) {
            $name = $method . '_' . $path;
        }
        $this->router->add($name, $method, $path, $callback, secured: $secured);
        if ($method === 'GET') {
            $this->router->add($name, 'GET', $path, $callback, secured: $secured);
        }
    }

    public function redirect(string $from, string $to, int $code = 302) {
        $this->router->add('redirect_', 'GET', $from, function () use ($to, $code) {
            return new Response('', $code, [
                'location' => $to
            ]);
        });
    }

    public function render(string $path, array $context = []): string
    {
        return $this->templateRenderer->render($path, $this->currentRequest, $context);
    }

    public function errorHandler(int $code, callable|string $handler): Response
    {
        $this->errorHandlers[$code] = $handler;
    }

    private function handleError()
    {

    }

    public function source(string $name): Source
    {
        return $this->locator->get('source_' . $name);
    }

    public function addDefaultHeader(string $name, string $value)
    {
        $this->defaultHeaders[$name] = $value;
    }
}
