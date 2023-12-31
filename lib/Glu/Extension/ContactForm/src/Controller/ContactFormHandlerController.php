<?php

namespace Glu\Extension\ContactForm\Controller;

use Glu\Controller;
use Glu\DataSource\Source;
use Glu\Http\Request;
use Glu\Http\Response;

final class ContactFormHandlerController implements Controller
{
    private Source $source;
    private string $pathPrefix;
    private string $successPath;

    public function __construct(Source $source, string $pathPrefix, string $successPath)
    {
        $this->source = $source;
        $this->pathPrefix = $pathPrefix;
        $this->successPath = $successPath;
    }

    public function handle(Request $request, Response $response, array $args): void
    {
        if ($request->isPost()) {
            $this->source->insert('dev.glu.contact_form.messages', [
                'email' => $request->form('email'),
                'message' => $request->form('message')
            ]);

            $response->setStatusCode(301);
            $response->addHeader('location', $this->pathPrefix . $this->successPath);
        }
    }

}
