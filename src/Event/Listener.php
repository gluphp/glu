<?php

namespace Glu\Event;

final class Listener
{
    private string $event;

    private string|\Closure $action;

    public function __construct(string $event, string|\Closure $action)
    {
        $this->event = $event;
        $this->action = $action;
    }

    public function event(): string
    {
        return $this->event;
    }

    public function action(): string|\Closure
    {
        return $this->action;
    }
}
