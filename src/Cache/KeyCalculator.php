<?php

namespace Glu\Cache;

use Glu\Http\Request;

interface KeyCalculator
{
    public function key(Request $request): string;
}
