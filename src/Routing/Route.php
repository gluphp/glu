<?php declare(strict_types = 1);

namespace Glu\Routing;

final class Route
{
    /** @var string[]  */
    private array $methods;

    public function __construct(
        private readonly string $name,
        string|array $methods,
        private readonly string $path,
        private readonly \Closure $controller
    )
    {
        if (\is_string($methods)) {
            $methods = [$methods];
        }
        $this->methods = $methods;
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

    public function controller(): callable
    {
        return $this->controller;
    }
}
