<?php

namespace Glu\Event\Lifecycle;

use Glu\Event\LifecycleEvent;
use Glu\Http\Request;
use Glu\Http\Response;

abstract class BaseLifecycleEvent implements LifecycleEvent
{
    protected Request $request;
    protected ?Response $response;
    protected bool $stopPropagation = false;
    protected bool $responseSet = false;

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
