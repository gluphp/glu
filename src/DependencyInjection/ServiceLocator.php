<?php declare(strict_types = 1);

namespace Glu\DependencyInjection;

final class ServiceLocator {

    /** @var Service[] */
    private array $definitions;
    private array $synthetic;

    /**
     * @param Service[] $services
     */
    public function __construct(array $definitions)
    {
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

    public function get(string $id): ?object
    {
        if (\array_key_exists($id, $this->synthetic)) {
            return $this->synthetic[$id];
        }

        if (\array_key_exists($id, $this->definitions)) {
            return $this->instantiate($this->definitions[$id]);
        }

        return null;
    }

    public function add(Service $definition): void
    {
        $this->definitions[$definition->name()] = $definition;
    }

    public function addSynthetic(string $name, $service): void
    {
        $this->synthetic[$name] = $service;
    }
}
