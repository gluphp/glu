<?php

namespace Glu\Event;

use Glu\DependencyInjection\ServiceLocator;

final class EventDispatcher
{
    /** @var array<string, Listener[]> */
    private array $listeners;

    private ServiceLocator $locator;

    /**
     * @param Listener[] $listeners
     */
    public function __construct(array $listeners, ServiceLocator $locator)
    {
        $this->locator = $locator;
        $this->listeners = [];
        foreach ($listeners as $listener) {
            if (false === \array_key_exists($listener->event(), $this->listeners)) {
                $this->listeners[$listener->event()] = [];
            }

            $this->listeners[$listener->event()][] = $listener;
        }
    }

    public function dispatch(Event $event)
    {
        $stop = false;
        foreach ($this->listeners[$event->name()] ?? [] as $listener) {
            if (\is_callable($listener->action())) {
                $listener->action()($event, $stop);
            } else {
                $this->locator->get($listener->action())($event, $stop);
            }

            if ($stop) {
                break;
            }
        }
    }
}
