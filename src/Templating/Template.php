<?php declare(strict_types = 1);

namespace Glu\Templating;

final class Template {
    public readonly string $path;
    public readonly array $variables;
    public readonly array $headers;

    public function __construct(string $path, array $variables = [], array $headers = [])
    {
        $this->path = $path;
        $this->variables = $variables;
        $this->headers = $headers;
    }
}
