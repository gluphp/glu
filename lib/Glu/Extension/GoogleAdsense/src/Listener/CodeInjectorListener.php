<?php

namespace Glu\Extension\GoogleAdsense\Listener;

use Glu\Event\Event;
use Glu\Event\Listener;

final class CodeInjectorListener implements Listener
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function event(): string
    {
        return 'glu.response_ready';
    }

    public function action(Event $event): string|\Closure
    {
        $event->response()->replace(
            '#</head>#',
            '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client="'.$this->id.' crossorigin="anonymous"></script></head>',
            1
        );
    }

}
