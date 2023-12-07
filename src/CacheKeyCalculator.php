<?php

namespace Glu;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Glu\Http\Request;
use Glu\Http\Response;

class CacheKeyCalculator
{
    public function key(Request $request): string
    {
        return md5(
            $request->method.
            $request->path()
        );
    }
}
