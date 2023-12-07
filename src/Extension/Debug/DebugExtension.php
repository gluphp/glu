<?php

namespace Glu\Extension\Debug;

use Glu\Event\Event;
use Glu\Event\Listener;
use Glu\Extension\BaseExtension;
use Glu\Routing\Route;

final class DebugExtension extends BaseExtension
{
    public function name(): string
    {
        return 'debuug';
    }

    public function routes(): array
    {
        return [
            //new Route('tomato_debug_profiler', 'GET', '/__profiler', )
        ];
    }

}
