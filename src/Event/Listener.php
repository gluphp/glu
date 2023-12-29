<?php

namespace Glu\Event;

interface Listener
{
    public function event(): string;

    public function action(Event $event): string|\Closure;
}
