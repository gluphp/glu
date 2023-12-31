<?php

namespace Glu\Event;

use Psr\Container\ContainerInterface;

final class EventDispatcher
{
    /** @var array<string, ListenerImp[]> */
    private array $listeners;

    private ContainerInterface $container;

    /**
     * @param ListenerImp[] $listeners
     */
    public function __construct(array $listeners, ContainerInterface $container)
    {
        $this->container = $container;
        $this->listeners = [];
        foreach ($listeners as $listener) {
            $this->register($listener);
        }
    }

    public function register(ListenerImp $listener): void
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
            if (\is_callable($listener->action())) {
                $listener->action()($event, $stop);
            } else {
                $this->container->get($listener->action())($event, $stop);
            }

            if ($stop) {
                break;
            }
        }

        return $event;
    }
}
