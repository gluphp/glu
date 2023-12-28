<?php

namespace Glu\Event;

use Glu\DependencyInjection\Container;

final class EventDispatcher
{
    /** @var array<string, Listener[]> */
    private array $listeners;

    private Container $locator;

    /**
     * @param Listener[] $listeners
     */
    public function __construct(array $listeners, Container $locator)
    {
        $this->locator = $locator;
        $this->listeners = [];
        foreach ($listeners as $listener) {
            $this->register($listener);
        }
    }

    public function register(Listener $listener): void
    {
        if (false === \array_key_exists($listener->event(), $this->listeners)) {
            $this->listeners[$listener->event()] = [];
        }
        $this->listeners[$listener->event()][] = $listener;
    }

    public function dispatch(Event $event): Event
    {
        $stop = false;
        foreach ($this->listeners[$event->name()] ?? [] as $listener) {
            \var_dump('yes');
            if (\is_callable($listener->action())) {
                $listener->action()($event, $stop);
            } else {
                $this->locator->get($listener->action())($event, $stop);
            }

            if ($stop) {
                break;
            }
        }

        return $event;
    }
}
