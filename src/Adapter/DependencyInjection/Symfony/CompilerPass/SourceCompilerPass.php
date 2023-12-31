<?php

namespace Glu\Adapter\DependencyInjection\Symfony\CompilerPass;

use Glu\DataSource\Source;
use Glu\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class SourceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $sources = $container->getParameter('glu.sources');
        foreach ($sources as $id => $context) {
            $container->register('source:' . $id, Source::class)
                ->setFactory([new Reference(Container::SERVICE_DATA_SOURCE_FACTORY), 'create'])
                ->addArgument($context);
        }

        $sourceFactories = [];
        foreach ($container->findTaggedServiceIds(Container::TAG_SOURCE_FACTORY) as $id => $tags) {
            $sourceFactories[] = new Reference($id);
        }
        $container->getDefinition(Container::SERVICE_DATA_SOURCE_FACTORY)
            ->setArgument('$sourceFactories', $sourceFactories);
    }

}
