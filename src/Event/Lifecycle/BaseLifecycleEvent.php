<?php

namespace Glu\Event\Lifecycle;

use Glu\Event\Event;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\MatchResult;

final class RouteMatchedEvent implements Event
{
    private Request $request;
    private ?Response $response;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
        $this->response = null;
    }

    public function name(): string
    {
        return 'life.route_matched';
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function setMatchResult(MatchResult $matchResult) {
        $this->matchResult = $matchResult;
    }
}
