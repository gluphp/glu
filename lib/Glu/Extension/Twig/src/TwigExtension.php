<?php declare(strict_types = 1);

namespace Glu\Extension\Twig;

use Glu\Extension\BaseExtension;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;
use Psr\Container\ContainerInterface;

final class TwigExtension extends BaseExtension
{

    public function __construct()
    {
    }

    public static function load(ContainerInterface $container, array $context): static
    {
        return new self();
    }

    public function name(): string
    {
        return 'dev.glu.twig';
    }

    public function services(): array
    {
        return [
            new \Glu\DependencyInjection\ServiceDefinition(
                '',
                \Glu\Extension\Twig\Templating\TwigEngine::class,
                [
                    'glu.templating_directories',
                    'glu.router',
                    'glu.cache_dir'
                ],
                ['glu.templating_engine']
            )
        ];
    }


}
