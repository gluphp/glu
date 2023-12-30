<?php

namespace Glu\Extension\ContactForm\Controller;

use Glu\Controller;
use Glu\DataSource\Source;
use Glu\Http\Request;
use Glu\Http\Response;

final class ContactFormHandler implements Controller
{
    private Source $source;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function handle(Request $request, Response $response, array $args): void
    {
        if ($request->method() === 'POST') {
            $this->source->insert('dev.glu.contact_form.messages', [
                'email' => $request->form('email'),
                'message' => $request->form('message')
            ]);

            $response->setStatusCode(301);
            $response->addHeader('location', $this->pathPrefix . $this->successPath);
        }
    }

}
