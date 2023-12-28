<?php

namespace Glu\Event\Lifecycle;

use Glu\Http\Request;
use Glu\Http\Response;

final class ExceptionThrownEvent extends BaseLifecycleEvent
{
    private \Throwable $exception;

    public function __construct(
        Request $request,
        ?Response $response,
        \Throwable $exception
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->exception = $exception;
    }

    public function name(): string
    {
        return 'glu.exception_thrown';
    }

    public function exception(): \Throwable
    {
        return $this->exception;
    }
}
