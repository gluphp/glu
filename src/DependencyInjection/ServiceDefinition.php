<?php

namespace Glu\DependencyInjection;

final class ServiceDefinition
{
    private string $name;
    private string $fqn;
    private array $arguments;
    private array $tags;
    private bool $isFactory;
    private ?string $factoryMethod;

    public function __construct(
        string $name,
        string $fqn,
        array $arguments = [],
        array $tags = [],
        bool $isFactory = false,
        ?string $factoryMethod = null
    )
    {
        $this->name = $name;
        $this->fqn = $fqn;
        $this->arguments = $arguments;
        $this->tags = $tags;
        $this->isFactory = $isFactory;
        $this->factoryMethod = $factoryMethod;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function fqn(): string
    {
        return $this->fqn;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function isFactory(): bool
    {
        return $this->isFactory;
    }

    public function factoryMethod(): string
    {
        return $this->factoryMethod;
    }
}
