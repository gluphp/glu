<?php declare(strict_types = 1);

namespace Glu\Templating;

use Glu\DependencyInjection\Container;

final class EngineResolver {
    private Container $container;
    private bool $initialized;
    /** @var string[] */
    private array $engineNames;
    /** @var TemplateRenderer[] */
    private array $engines;

    public function __construct(TemplateRenderer ...$renderers)
    {
        $this->engines = [];
        $this->initialized = false;
    }

    private function initialize() {
        if ($this->initialized) {
            return;
        }

        foreach ($this->engineNames as $name) {
            $this->engines[] = $this->container->get($name);
        }
        $this->initialized = true;
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

    public function resolve(string $path): TemplateRenderer {
        $this->initialize();

        foreach ($this->engines as $renderer) {
            if ($renderer->supports($path)) {
                return $renderer;
            }
        }

        throw new UnsupportedTemplateException();
    }
}
