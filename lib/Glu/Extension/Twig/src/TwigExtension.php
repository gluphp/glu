<?php

declare(strict_types=1);

namespace Glu\Extension\Twig;

use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\Reference;
use Glu\DependencyInjection\Service;
use Glu\Extension\BaseExtension;
use Glu\Extension\Twig\Templating\TwigEngine;

final class TwigExtension extends BaseExtension
{
    public function __construct()
    {
    }

    public static function load(Container $container, array $context): static
    {
        return new self();
    }

    public function name(): string
    {
        return 'dev.glu.twig';
    }

    public function containerDefinitions(): array
    {
        return [
            new Service(
                'glu.ext.twig.engine',
                TwigEngine::class,
                [
                    '%glu.templating.directories%',
                    new Reference('glu.router'),
                    new Reference('glu.environment'),
                    '%glu.templating.functions%',
                    '%glu.cache_dir%'
                ],
                [Container::TAG_TEMPLATING_ENGINE]
            )
        ];
    }


}
