<?php

namespace Glu\Event;

final class RouteWasMatchedEvent implements Event
{
    public function __construct(
        public readonly string $name,
        public readonly array $context = []
    ) {}
}
