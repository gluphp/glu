<?php

namespace Glu\Extension;

use Glu\DataSource\Source;
use Glu\DependencyInjection\ServiceLocator;
use Glu\Routing\Route;
use Glu\Templating\_Function;

interface Extension
{
    public static function load(ServiceLocator $locator, array $context): static;

    public function name(): string;

    public function templateDirectories(): array;
    /* @return Route[] */
    public function routes(): array;
    /* @return Source[] */
    public function dataSources(): array;
    public function services(): array;

    public function listeners(): array;
    /* @return _Function[] */
    public function rendererFunctions(): array;
}
