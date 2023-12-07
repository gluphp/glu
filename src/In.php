<?php

namespace Glu;

use Glu\DataSource\Source;
use Glu\DependencyInjection\ServiceLocator;
use Glu\Http\Request;

final class In
{
    private Request $request;
    private array $parameters;
    private ServiceLocator $locator;
    public function __construct(
        Request $request,
        array $parameters,
        ServiceLocator $locator
    )
    {
        $this->request = $request;
        $this->parameters = $parameters;
        $this->locator = $locator;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function parameter(string $name)
    {
        return $this->parameters[$name];
    }

    public function service(string $name)
    {
        return $this->locator->get($name);
    }

    public function source(string $name): Source
    {
        return $this->locator->get('source_' . $name);
    }
}
