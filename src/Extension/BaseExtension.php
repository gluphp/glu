<?php

namespace Glu\Extension;

use Glu\Templating\Engine;

abstract class BaseExtension implements Extension
{
    public function templateDirectories(): array
    {
        return [];
    }
    public function routes(): array {
        return [];
    }
    public function dataSources(): array {
        return [];
    }

    public function services(): array
    {
        return [];
    }

    public function listeners(): array
    {
        return [];
    }

    public function rendererFunctions(): array
    {
        return [];
    }

    public function templateRenderers(): array
    {
        return [];
    }
}
