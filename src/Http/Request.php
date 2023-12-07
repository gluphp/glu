<?php declare(strict_types = 1);

namespace Glu\Http;

final class Request {
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        private readonly array $querystring,
        public readonly array  $headers,
        public readonly array  $payload,
        private array $files,
        private array $cookies,
        private array $server

    ) {}

    public static function new(): self {
        \parse_str($_SERVER['QUERY_STRING'], $query);

        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            \parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
            $query,
            getallheaders(),
            $_POST,
            $_FILES,
            $_COOKIE,
            $_SERVER
        );
    }

    public function query(string $key, int|string|float $default = null): null|int|string
    {
        return $this->querystring[$key] ?? $default;
    }

    public function form(string $key, int|string|float $default = null): null|int|string
    {
        return $this->payload[$key] ?? $default;
    }

    public function cookie(string $name, $default = null)
    {
        return $this->cookies[$name] ?? $default;
    }

    public function file(string $name, $default = null)
    {
        return $this->cookies[$name] ?? $default;
    }

    public function clientIp(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR'] ?? '';
    }

    public function scheme(): string
    {
        if (\array_key_exists('REQUEST_SCHEME', $this->server)) {
            return $this->server['REQUEST_SCHEME'];
        }

        if ($this->server['HTTPS']??'off' === 'on') {
            return 'https';
        }

        return 'http';
    }

    public function host(): string
    {
        return $this->server['HTTP_HOST'];
    }

    public function path(): string
    {
        return $this->server['REQUEST_URI'];
    }

    public function url(): string
    {
        return $this->scheme() . '://' . $this->host() . $this->path;
    }
}
