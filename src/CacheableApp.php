<?php

declare(strict_types=1);

namespace Glu;

use Glu\Cache\CacheKeyCalculator;
use Glu\Http\Request;
use Glu\Http\Response;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class CacheableApp implements AppInterface
{
    private ?CacheItemPoolInterface $cache;
    private AppInterface $app;

    public function __construct(
        AppInterface $app,
        CacheItemPoolInterface $cache = null
    ) {
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
        $this->app->addPath('GET', $path, $callback, $name);
    }

    public function addPath(
        string $method,
        string $path,
        callable|string $callback,
        ?string $name,
        ?string $secured = null
    ) {
        $this->app->addPath($method, $path, $callback, $name, $secured);
    }

    public function addRedirect(string $from, string $to, int $code = 302)
    {
        $this->app->addRedirect($from, $to, $code);
    }
}
