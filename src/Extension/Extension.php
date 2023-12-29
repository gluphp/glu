<?php

namespace Glu\Extension;

use Glu\DataSource\Source;
use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\ServiceDefinition;
use Glu\Routing\Route;
use Glu\Templating\_Function;
use Glu\Templating\Engine;
use Psr\Container\ContainerInterface;

interface Extension
{
    public static function load(ContainerInterface $container, array $context): static;

    public function name(): string;

    public function templateDirectories(): array;
    /* @return Route[] */
    public function routes(): array;
    /* @return Source[] */
    public function dataSources(): array;
    /** @return ServiceDefinition[] */
    public function services(): array;

    public function listeners(): array;
    /* @return _Function[] */
    public function rendererFunctions(): array;

    /** @return Engine[] */
    public function templateRenderers(): array;
}
