<?php declare(strict_types = 1);

namespace Glu\Templating;

use Glu\DependencyInjection\Container;
use Glu\Http\Request;

final class RendererFactory {
    public static function create(Engine ...$engines) {
        return new Renderer($engines);
    }
}
