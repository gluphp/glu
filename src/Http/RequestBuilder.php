<?php

namespace Glu\Http;

final class RequestBuilder
{
    private string $method;
    private string $path;
    private array $querystring;
    private array $headers;
    private array $payload;
    private array $files;
    private array $cookies;
    private array $server;

    public function __construct(
        string $method = 'GET',
        string $path = '/'
    ) {
        $this->method = $method;
        $this->path = $path;
        $this->querystring = [];
        $this->headers = [];
        $this->payload = [];
        $this->files = [];
        $this->cookies = [];
        $this->server = [];
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function create(): Request
    {
        return new Request(
            $this->method,
            $this->path,
            $this->querystring,
            $this->headers,
            $this->payload,
            $this->files,
            $this->cookies,
            $this->server
        );
    }
}
