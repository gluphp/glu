<?php

namespace Glu\Templating;

interface _Function
{
    public function name(): string;

    public function callable(): callable;

    public function escape(): bool;
}
