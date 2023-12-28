<?php

namespace Glu\Event\Lifecycle;

use Glu\Event\Event;
use Glu\Http\Request;
use Glu\Http\Response;

final class ResponseReadyEvent extends BaseLifecycleEvent
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
        return 'glu.response_ready';
    }
}
