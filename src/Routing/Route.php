<?php declare(strict_types = 1);

namespace Glu\Routing;

final class Route
{
    private string $name;
    /** @var string[]  */
    private array $methods;
    private string $path;
    private \Closure $controller;

    public function __construct(
        string $name,
        string|array $methods,
        string $path,
        \Closure $controller
    )
    {
        $this->name = $name;

        if (\is_string($methods)) {
            $methods = [$methods];
        }
        $this->methods = $methods;

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

    public function controller(): callable
    {
        return $this->controller;
    }
}
