<?php

namespace Glu\Cache;

interface KeyCalculator
{
    public function key(RequestInterface $request): string;
}
