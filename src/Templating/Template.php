<?php

declare(strict_types=1);

namespace Glu\Templating;

final class Template
{
    private readonly string $path;
    private readonly array $context;

    public function __construct(string $path, array $context = [])
    {
        $this->path = $path;
        $this->context = $context;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function context(): array
    {
        return $this->context;
    }
}
