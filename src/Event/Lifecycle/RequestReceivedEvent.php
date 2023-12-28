<?php

namespace Glu\Event\Lifecycle;

use Glu\Event\Event;
use Glu\Event\LifecycleEvent;
use Glu\Http\Request;
use Glu\Http\Response;
use Psr\Http\Message\RequestInterface;

final class RequestReceivedEvent extends BaseLifecycleEvent
{
    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    public function name(): string
    {
        return 'glu.request_received';
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
