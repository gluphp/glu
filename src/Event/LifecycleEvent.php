<?php

namespace Glu\Event;

use Glu\Http\Request;
use Glu\Http\Response;

interface LifecycleEvent extends Event
{
    public function request(): Request;
    public function response(): Response;
    public function setResponse(Response $response): void;
    public function setResponseAndStopPropagation(Response $response): void;
    public function responseHasBeenSet(): bool;
}
