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

final class AppBuilder
{
    private array $routes;
    private array $defaultHeaders;
    /** @var Source[] */
    private array $sources;
    private LoggerInterface $logger;

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

    }

    public function build(): AppInterface
    {
        return new App();
    }

    public function addPath(
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

    public function addRedirect(string $from, string $to, int $code = 302) {
        $this->router->add('redirect_', 'GET', $from, function () use ($to, $code) {
            return new Response('', $code, [
                'location' => $to
            ]);
        });
    }

    public function errorHandler(int $code, callable|string $handler): Response
    {
        $this->errorHandlers[$code] = $handler;
    }

    public function addDefaultHeader(string $name, string $value)
    {
        $this->defaultHeaders[$name] = $value;
    }
}
