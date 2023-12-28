<?php


use Glu\DependencyInjection\Container;
use Glu\Extension\BaseExtension;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;

final class TwigExtension extends BaseExtension
{

    public function __construct()
    {
    }

    public static function load(Container $locator, array $context): static
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
                \Glu\Extension\Twig\Templating\TwigTemplateRenderer::class,
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
