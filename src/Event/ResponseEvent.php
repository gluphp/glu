<?php

namespace Glu\Event;

use Glu\Http\Request;
use Glu\Http\Response;

final class ResponseEvent implements Event
{
    private readonly string $name;
    private Response $response;
    private Request $request;

    public function __construct(
        string $name,
        Response $response,
        Request $request
    ) {
        $this->name = $name;
        $this->response = $response;
        $this->request = $request;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function overrideResponse(Response $response): void
    {
        $this->response = $response;
    }
}
