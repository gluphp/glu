<?php

namespace Glu\Adapter\DependencyInjection\Symfony\CompilerPass;

use Glu\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TemplatingEngineCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $engines = [];
        $rendererFactory = $container->getDefinition(Container::SERVICE_TEMPLATING_RENDERER_FACTORY);
        foreach ($container->findTaggedServiceIds(Container::TAG_TEMPLATING_ENGINE) as $id => $tags) {
            $engines[] = new Reference($id);
        }
        $rendererFactory->setArgument('$engines', $engines);

        $functions = [];
        foreach ($container->findTaggedServiceIds(Container::TAG_TEMPLATING_FUNCTION) as $id => $tags) {
            $functions[] = new Reference($id);
        }
        $container->setParameter('glu.templating.functions', $functions);
    }

}
