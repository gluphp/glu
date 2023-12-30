<?php

namespace Glu\Adapter\DependencyInjection\Symfony\CompilerPass;

use Glu\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

final class TemplatingEngineCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $engines = [];
        $renderer = $container->getDefinition(Container::SERVICE_TEMPLATING_RENDERER_FACTORY);
        foreach ($container->findTaggedServiceIds(Container::TAG_TEMPLATING_ENGINE) as $id => $tags) {
            $engines[] = new Reference($id);
        }
        $renderer->addArgument($engines);

        $functions = [];
        foreach ($container->findTaggedServiceIds(Container::TAG_TEMPLATING_FUNCTION) as $id => $tags) {
            $functions[] = new Reference($id);
        }
        $container->setParameter('glu.templating.functions', $functions);
    }

}
