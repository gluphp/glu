<?php

namespace Glu\Event\Lifecycle;

use Glu\Event\Event;
use Glu\Event\LifecycleEvent;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\MatchResult;

abstract class BaseLifecycleEvent implements LifecycleEvent
{
    private Request $request;
    private ?Response $response;
    private bool $stopPropagation = false;
    private bool $responseSet = false;

    public function request(): Request
    {
        return $this->request;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function stopPropagation(): void
    {
        $this->stopPropagation = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopPropagation;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
        $this->responseSet = true;
    }

    public function setResponseAndStopPropagation(Response $response): void
    {
        $this->setResponse($response);
        $this->stopPropagation = true;
    }

    public function responseHasBeenSet(): bool
    {
        return $this->responseSet;
    }


}
