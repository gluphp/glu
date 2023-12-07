<?php

namespace Glu;

use Glu\Http\Request;
use Glu\Http\Response;

interface AppInterface
{
    public function handle(Request $request, bool $send = true): ?Response;

    public function send(Response $response): void;

    public function addPath(string $method, string $path, callable|string $callback, ?string $name, ?string $secured = null);

    public function addRedirect(string $from, string $to, int $code = 302);
}
