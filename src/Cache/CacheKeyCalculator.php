<?php

namespace Glu\Cache;

use Glu\Http\Request;

final class CacheKeyCalculator implements KeyCalculator
{
    public function key(Request $request): string
    {
        return md5(
            $request->method().
            $request->path()
        );
    }
}
