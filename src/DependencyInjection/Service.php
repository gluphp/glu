<?php

namespace Glu\DependencyInjection;

final class Service
{
    private string $name;
    private string $fqn;
    private array $arguments;
    private bool $isFactory;
    private ?string $factoryMethod;

    public function __construct(
        string $name,
        string $fqn,
        array $arguments = [],
        bool $isFactory = false,
        ?string $factoryMethod = null
    )
    {
        $this->name = $name;
        $this->fqn = $fqn;
        $this->arguments = $arguments;
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

    public function isFactory(): bool
    {
        return $this->isFactory;
    }

    public function factoryMethod(): string
    {
        return $this->factoryMethod;
    }
}
