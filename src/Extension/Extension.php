<?php

namespace Glu\Extension;

use Glu\DependencyInjection\Definition;
use Glu\Routing\Route;

interface Extension
{
    public function name(): string;

    public function configuration(): array;

    /* @return Route[] */
    public function routes(): array;

    /** @return Definition[] */
    public function containerDefinitions(): array;
}
