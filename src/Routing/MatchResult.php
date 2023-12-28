<?php declare(strict_types = 1);

namespace Glu\Routing;

final class RouteMatch {

    public function __construct(
        public readonly bool $found,
        public readonly \Closure|string|null $controller = null,
        public readonly ?array $parameters = [],
        public readonly ?string $secured = null

    )
    {
    }

    public static function found(\Closure $controller, array $parameters = [], ?string $secured = null): self
    {
        return new RouteMatch(true, $controller, $parameters, $secured);
    }

    public static function notFound(): self
    {
        return new self(false);
    }
}
