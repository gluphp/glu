<?php

namespace Glu;

use Glu\Http\Request;
use Glu\Http\Response;

interface Controller
{
    public function handle(Request $request, Response $response, array $args): void;
}
