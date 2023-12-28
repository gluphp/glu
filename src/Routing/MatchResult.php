<?php declare(strict_types = 1);

namespace Glu\Routing;

final class MatchResult {

    private bool $found;
    private ?Route $route;
    private array $parameters;

    public function __construct(
        bool   $found,
        ?Route $route = null,
        array  $parameters = []
    )
    {
        $this->found = $found;
        $this->route = $route;
        $this->parameters = $parameters;
    }

    public static function createFound(Route $route, array $parameters = []): self
    {
        return new self(true, $route, $parameters);
    }

    public static function createNotFound(): self
    {
        return new self(false);
    }

    public function isFound(): bool
    {
        return $this->found;
    }

    public function route(): ?Route
    {
        return $this->route;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }


}
