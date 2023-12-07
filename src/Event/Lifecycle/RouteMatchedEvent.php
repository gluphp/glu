<?php

namespace Glu\Event\Lifecycle;

use Glu\Event\Event;
use Glu\Http\Request;
use Glu\Http\Response;

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

    public function response(): ?Response
    {
        return $this->response;
    }

    public function replaceRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
