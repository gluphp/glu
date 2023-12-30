<?php

namespace Glu\Extension\ContactForm\Controller;

use Glu\Controller;
use Glu\DataSource\Source;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Templating\Renderer;

final class AdminListController implements Controller
{
    private Renderer $renderer;
    private Source $source;

    public function __construct(
        Renderer $renderer,
        Source $source
    ) {
        $this->renderer = $renderer;
        $this->source = $source;
    }

    public function handle(Request $request, Response $response, array $args): void
    {
        $response->contents = $this->renderer->render(
            'admin_contact.html.twig',
            [
                'items' => $this->source->fetch('dev.glu.contact_form.messages')
            ]
        );
    }

}
