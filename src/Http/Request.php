<?php declare(strict_types = 1);

namespace Glu\Http;

use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\ServerRequestInterface;

final class Request {

    private ServerRequestInterface $psr7Request;
    private array $query;

    public function __construct(ServerRequestInterface $psr7Request) {
        $this->psr7Request = $psr7Request;
        $this->query = [];
        \parse_str($psr7Request->getUri()->getQuery(), $this->query);
    }

    public static function new(): self {
        $httpFactory = new HttpFactory();

        return new self(
            $httpFactory->createServerRequest(
                $_SERVER['REQUEST_METHOD'] ?? 'GET',
                $httpFactory->createUri($_SERVER['REQUEST_URI']),
                $_SERVER
            )
        );
    }

    public function psr7Request(): ServerRequestInterface
    {
        return $this->psr7Request;
    }

    public function query(string $key, int|string|float $default = null): null|int|string
    {
        return $this->querystring[$key] ?? $default;
    }

    public function form(string $key, int|string|float $default = null): null|int|string
    {
        //$this->psr7Request->getParsedBody();
        return $this->payload[$key] ?? $default;
    }

    public function cookie(string $name, $default = null)
    {
        return $this->psr7Request->getCookieParams()[$name] ?? $default;
    }

    public function file(string $name, $default = null)
    {
        return $this->psr7Request->getUploadedFiles()[$name] ?? null;
    }

    public function clientIp(): string
    {
        return $this->psr7Request->getServerParams()[HTTP_X_FORWARDED_FOR] . '';
    }

    public function scheme(): string
    {
        return $this->psr7Request->getUri()->getScheme();
    }

    public function host(): string
    {
        return $this->psr7Request->getUri()->getHost();
    }

    public function path(): string
    {
        return $this->psr7Request->getUri()->getPath();
    }

    public function url(): string
    {
        return $this->psr7Request->getUri()->getPath();
    }

    public function method(): string
    {
        return $this->psr7Request->getMethod();
    }
}
