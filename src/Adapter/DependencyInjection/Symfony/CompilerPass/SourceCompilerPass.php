<?php

namespace Glu\Adapter\DependencyInjection\Symfony\CompilerPass;

use Glu\DataSource\Source;
use Glu\DataSource\SourceFactoryFactory;
use Glu\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

final class SourceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $sourceFactories = [];
        $sourceManager = $container->getDefinition(Container::SERVICE_DATA_SOURCE_MANAGER);

        $sources = $container->getParameter('glu.sources');
        foreach ($sources as $id => $context) {
            $container->register('source:' . $id, Source::class)
                ->setFactory([SourceFactoryFactory::class, 'create'])
                ->addArgument($context);
        }

        foreach ($container->findTaggedServiceIds(Container::TAG_SOURCE_FACTORY) as $id => $tags) {
            $sourceFactories[] = new Reference($id);
        }
        $sourceManager->addArgument($sourceFactories);
    }

}
