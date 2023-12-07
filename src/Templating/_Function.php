<?php

namespace Glu\Templating;

final class _Function
{
    private string $name;
    private \Closure $callable;
    private bool $escape;

    public function __construct(string $name, callable $callable, bool $escape = true)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->escape = $escape;
    }

    public function name(): string {
        return $this->name;
    }

    public function callable(): callable {
        return $this->callable;
    }

    public function escape(): bool
    {
        return $this->escape;
    }
}
