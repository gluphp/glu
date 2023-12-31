<?php

declare(strict_types=1);

namespace Glu\Routing;

final class Route
{
    private string $name;
    /** @var string[]  */
    private array $methods;
    private string $path;
    private \Closure|string $controller;

    public function __construct(
        string $name,
        string|array $methods,
        string $path,
        \Closure|string $controller
    ) {
        $this->name = $name;

        if (\is_string($methods)) {
            $methods = [\mb_strtolower($methods)];
        }
        $this->methods = \array_map(function ($method) {
            return \mb_strtolower($method);
        }, $methods);

        $this->path = $path;
        $this->controller = $controller;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function methods(): array
    {
        return $this->methods;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function controller(): callable|string
    {
        return $this->controller;
    }
}
