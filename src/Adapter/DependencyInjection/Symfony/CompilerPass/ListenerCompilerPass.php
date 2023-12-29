<?php

namespace Glu\Adapter\DependencyInjection\Symfony\CompilerPass;

use Glu\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ListenerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $renderer = $container->getDefinition(Container::SERVICE_TEMPLATING_RENDERER_FACTORY);
        foreach ($container->findTaggedServiceIds(Container::TAG_TEMPLATING_ENGINE) as $id => $tags) {
            $renderer->addArgument('@' . $id);
        }
    }

}
