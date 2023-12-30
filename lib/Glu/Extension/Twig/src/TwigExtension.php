<?php declare(strict_types = 1);

namespace Glu\Extension\Twig;

use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\Reference;
use Glu\DependencyInjection\Service;
use Glu\Extension\BaseExtension;
use Glu\Extension\Twig\Templating\TwigEngine;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;
use Psr\Container\ContainerInterface;

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
                    new Reference('glu.templating_directories'),
                    new Reference('glu.router'),
                    new Reference('glu.cache_dir')
                ],
                [Container::TAG_TEMPLATING_ENGINE]
            )
        ];
    }


}
