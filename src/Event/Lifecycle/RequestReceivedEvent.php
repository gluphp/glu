<?php

namespace Glu\Event\Lifecycle;

use Glu\Http\Request;

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
