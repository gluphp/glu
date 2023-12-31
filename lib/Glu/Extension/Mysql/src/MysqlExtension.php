<?php declare(strict_types = 1);

namespace Glu\Extension\Mysql;

use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\Reference;
use Glu\DependencyInjection\Service;
use Glu\Extension\BaseExtension;
use Glu\Extension\Mysql\Source\DbalSource;
use Glu\Extension\Twig\Templating\TwigEngine;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;
use Psr\Container\ContainerInterface;

final class MysqlExtension extends BaseExtension
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
        return 'dev.glu.mysql';
    }

    public function containerDefinitions(): array
    {
        return [
            new Service(
                'glu.ext.mysql.dbal.source_factory',
                DbalSource::class,
                [],
                [Container::TAG_SOURCE_FACTORY]
            )
        ];
    }


}
