<?php

namespace Glu\DependencyInjection;

final class Reference
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
