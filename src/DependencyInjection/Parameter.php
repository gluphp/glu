<?php

namespace Glu\DependencyInjection;

final class Parameter implements Definition
{
    private string $id;
    private string|int|bool|array $value;

    public function __construct(
        string $id,
        string|int|bool|array $value
    )
    {
        $this->id = $id;
        $this->value = $value;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function value(): string
    {
        return $this->value;
    }
}
