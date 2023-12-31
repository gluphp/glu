<?php

namespace Glu\Event\Lifecycle;

use Glu\Http\Request;
use Glu\Routing\MatchResult;

final class RouteMatchedEvent extends BaseLifecycleEvent
{
    private MatchResult $matchResult;

    public function __construct(
        Request $request,
        MatchResult $matchResult
    ) {
        $this->request = $request;
        $this->matchResult = $matchResult;
    }

    public function name(): string
    {
        return 'glu.route_matched';
    }

    public function matchResult(): MatchResult
    {
        return $this->matchResult;
    }

    public function setMatchResult(MatchResult $matchResult)
    {
        $this->matchResult = $matchResult;
    }
}
