<?php

namespace Glu\Extension;

abstract class BaseExtension implements Extension
{
    public function configuration(): array
    {
        return [];
    }
    public function routes(): array
    {
        return [];
    }
    public function dataSources(): array
    {
        return [];
    }

    public function containerDefinitions(): array
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
