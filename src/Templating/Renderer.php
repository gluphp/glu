<?php declare(strict_types = 1);

namespace Glu\Templating;

use Glu\DependencyInjection\Container;
use Glu\Http\Request;

final class Renderer {
    /** @var Engine[] */
    private array $engines;

    public function __construct(Engine ...$engines)
    {
        $this->engines = $engines;
    }

    public function render(string $path, Request $request, array $context = []): string {
        return $this->resolve($path)->render($path, $request, $context);
    }

    private function resolve(string $path): Engine {
        foreach ($this->engines as $renderer) {
            if ($renderer->supports($path)) {
                return $renderer;
            }
        }

        throw new UnsupportedTemplateException();
    }
}
