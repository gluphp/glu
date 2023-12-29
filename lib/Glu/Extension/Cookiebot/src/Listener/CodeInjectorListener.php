<?php

namespace Glu\Extension\Cookiebot\Listener;

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
            '<head>'."\n".'<script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="'.$this->id.'" data-blockingmode="auto" type="text/javascript"></script>',
            1
        );
    }

}
