<?php

declare(strict_types=1);

namespace Glu\Routing;

final class CompiledRoute
{
    private Route $route;
    private string $regex;
    private array $parameters;

    public function __construct(Route $route, string $regex, array $parameters)
    {
        $this->route = $route;
        $this->regex = $regex;
        $this->parameters = $parameters;
    }

    public function route(): Route
    {
        return $this->route;
    }

    public function regex(): string
    {
        return $this->regex;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }
}
