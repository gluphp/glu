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

final class CacheableApp implements AppInterface
{
    private ?CacheItemPoolInterface $cache;
    private AppInterface $app;

    public function __construct(
        AppInterface $app,
        CacheItemPoolInterface $cache = null
    )
    {
        $this->app = $app;
        $this->cache = $cache ?? new FilesystemAdapter(
            'glu',
            0,
            $cacheDir
        );
    }

    public function handle(Request $request, bool $send = true): Response
    {
        $cacheKey = (new CacheKeyCalculator())->key($request);

        $cachedResponse = $this->cache->getItem($cacheKey);
        if ($cachedResponse->isHit()) {
            $this->send($cachedResponse->get());

            return $cachedResponse->get();
        }

        $response = $this->app->handle($request, $send);

        $cachedResponse->set($response);
        $this->cache->save($cachedResponse);

        return $response;
    }

    public function send(Response $response): void
    {
        $this->app->send($response);
    }

    public function get(string $path, callable $callback, ?string $name = null): void
    {
        $this->addPath('GET', $path, $callback, $name);
    }

    public function getStatic(string $path, string $callback, ?string $name = null): void
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
        $this->app->addPath($method, $path, $callback, $name, $secured);
    }

    public function addRedirect(string $from, string $to, int $code = 302) {
        $this->app->addRedirect($from, $to, $code);
    }
}
