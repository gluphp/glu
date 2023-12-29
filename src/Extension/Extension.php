<?php

namespace Glu\Extension;

use Glu\DataSource\Source;
use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\Definition;
use Glu\DependencyInjection\Service;
use Glu\Routing\Route;
use Glu\Templating\ConcreteFunction;
use Glu\Templating\Engine;
use Psr\Container\ContainerInterface;

interface Extension
{
    public function name(): string;

    public function configuration(): array;

    /* @return Route[] */
    public function routes(): array;

    /** @return Definition[] */
    public function containerDefinitions(): array;
}
