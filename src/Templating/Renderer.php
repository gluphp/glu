<?php declare(strict_types = 1);

namespace Glu\Templating;

use Glu\DependencyInjection\Container;
use Glu\Http\Request;

final class Renderer {
    /** @var Engine[] */
    private array $engines;

    public function __construct(array $engines)
    {
        $this->engines = [];
    }

    public function registerEngine(string $serviceId)
    {
        $this->engineNames[] = $serviceId;
    }

    public function registerDirectory(string $directory): void
    {
        $directories = $this->container->get('glu.template.directories') ?? [];
        $directories[] = $directory;

        $this->container->setParameter(
            'glu.template.directories',
            $directories
        );
    }

    public function registerFunction(_Function $function): void
    {
        $functions = $this->container->get('glu.template.functions') ?? [];
        $functions[] = $function;

        $this->container->setParameter(
            'glu.template.functions',
            $functions
        );
    }

    public function resolve(string $path): Engine {
        $this->initialize();

        foreach ($this->engines as $renderer) {
            if ($renderer->supports($path)) {
                return $renderer;
            }
        }

        throw new UnsupportedTemplateException();
    }

    public function render(string $path, Request $request, array $context = []): string {
        return $this->resolve($path)->render($path, $request, $context);
    }
}
