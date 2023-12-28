<?php declare(strict_types = 1);

namespace Glu\Templating;

use Glu\DependencyInjection\Container;
use Glu\Http\Request;

final class RendererFactory {
    private Container $container;
    /** @var string[] */
    private array $engineNames;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->engineNames = [];
    }

    public static function create(array $ids) {
        return new Renderer(\array_map(function(string $serviceId) {
            return $this->container->get($serviceId);
        }, $ids));
    }
}
