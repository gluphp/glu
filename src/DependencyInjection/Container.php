<?php declare(strict_types = 1);

namespace Glu\DependencyInjection;

use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface {

    public const SERVICE_DATA_SOURCE_MANAGER = 'glu.data_source.manager';
    public const SERVICE_TEMPLATING_RENDERER_FACTORY = 'glu.templating.renderer_factory';
    public const SERVICE_TEMPLATING_RENDERER = 'glu.templating.renderer';

    public const TAG_LISTENER = 'glu.tag.listener';
    public const TAG_TEMPLATING_ENGINE = 'glu.tag.templating_engine';
    public const TAG_TEMPLATING_FUNCTION = 'glu.tag.templating_function';
    public const TAG_SOURCE_FACTORY = 'glu.tag.source_factory';

    private array $parameters;
    /** @var Service[] */
    private array $definitions;
    private array $synthetic;

    /**
     * @param Service[] $services
     */
    public function __construct(array $definitions, array $parameters = [])
    {
        $this->parameters = $parameters;
        $this->synthetic = [];
        $this->definitions = [];
        foreach ($definitions as $definition) {
            $this->definitions[$definition->name()] = $definition;
        }
    }

    private function instantiate(Service $definition) {
        $arguments = [];
        foreach ($definition->arguments() as $argument) {
            if (\str_starts_with($argument, '@')) {
                $arguments[] = $this->instantiate($this->definitions[\substr($argument, 1)]);
            } else {
                $arguments[] = $argument;
            }
        }

        if ($definition->isFactory()) {
            return \call_user_func([$definition->fqn(), $definition->factoryMethod()], ...$arguments);
        }

        return new ($definition->fqn())(...$arguments);
    }

    public function get(string $id)
    {
        if (\array_key_exists($id, $this->synthetic)) {
            return $this->synthetic[$id];
        }

        if (\array_key_exists($id, $this->parameters)) {
            return $this->parameters[$id];
        }

        if (\array_key_exists($id, $this->definitions)) {
            return $this->instantiate($this->definitions[$id]);
        }

        return null;
    }

    public function has(string $id): bool
    {
        return \array_key_exists($id, $this->synthetic) ||
            \array_key_exists($id, $this->definitions) ||
            \array_key_exists($id, $this->parameters)
            ;
    }

    public function set(Service $definition): void
    {
        $this->definitions[$definition->id()] = $definition;
    }

    public function setSynthetic(string $name, $service): void
    {
        $this->synthetic[$name] = $service;
    }

    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }
}
