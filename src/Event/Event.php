<?php

namespace Glu\Event;

interface Event
{
    public function name(): string;

    public function stopPropagation(): void;
    public function isPropagationStopped(): bool;
}
