<?php

declare(strict_types=1);

namespace Glu\Templating;

final class RendererFactory
{
    private array $engines;

    public function __construct(array $engines)
    {
        $this->engines = $engines;
    }

    public function create()
    {
        return new Renderer($this->engines);
    }
}
