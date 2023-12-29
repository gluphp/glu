<?php

namespace Glu\Extension\GoogleAnalytics\Listener;

use Glu\Event\Event;
use Glu\Event\Listener;
use Glu\Templating\Renderer;

final class CodeInjectorListener implements Listener
{
    private Renderer $renderer;
    private string $id;

    public function __construct(Renderer $renderer, string $id)
    {
        $this->renderer = $renderer;
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
            $this->renderer->render('code.html.twig', $event->request(), [
                'id' => $this->id
            ]) . '</head>',
            1
        );
    }

}
