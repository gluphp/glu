<?php

namespace Glu\Event\Lifecycle;

use Glu\Http\Request;
use Glu\Http\Response;

final class ControllerExecutedEvent extends BaseLifecycleEvent
{
    public function __construct(
        Request $request,
        Response $response
    ) {
        $this->request = $request;
        $this->response = $response;
    }

    public function name(): string
    {
        return 'glu.controller_executed';
    }
}
