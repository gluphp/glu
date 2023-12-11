<?php

namespace Glu\Extension\ContactForm;

use Glu\Adapter\DataSource\FilesystemSource;
use Glu\DataSource\Source;
use Glu\DependencyInjection\ServiceLocator;
use Glu\Event\Listener;
use Glu\Event\ResponseEvent;
use Glu\Extension\BaseExtension;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;
use Glu\Templating\_Function;

final class ContactFormExtension extends BaseExtension
{
    private Source $source;

    public function __construct(
        Source $source
    )
    {
        $this->source = $source;
    }

    public static function load(ServiceLocator $locator, array $context): static
    {
        return new self(
            $context['source'] ?? new FilesystemSource(
                $locator->get('data_directory') . '/dev.glu.contact_form'
            )
        );
    }

    public function name(): string
    {
        return 'dev.glu.contact_form';
    }

    public function rendererFunctions(): array
    {
        return [
            new _Function(
                'contact_form',
                function() {
                    return <<<CODE
<form action="/contact-handle" method="post">

    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required aria-label="Enter your name">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required aria-label="Enter your email">

    <label for="message">Message:</label>
    <textarea id="message" name="message" rows="4" maxlength="300" required aria-label="Type your message"></textarea>

    <button type="submit">Submit</button>

</form>
CODE;
                },
                false
            )
        ];
    }

    public function routes(): array
    {
        $source = $this->source;
        return [
            new Route(
                'aaa',
                ['GET', 'POST'],
                '/contact-handle',
                function (Request $request, Response $response, array $args) use ($source) {
                    if ($request->method === 'POST') {
                        $source->insert('dev.glu.contact_form.messages', [
                            'email' => $request->form('email'),
                            'message' => $request->form('message')
                        ]);

                        $response->statusCode = 301;
                        $response->headers['location'] = '/contact-success';
                    }
                }
            ),
            new Route(
                'aaa',
                'GET',
                '/admin/contact',
                function (Request $request, Response $response, array $args) use ($source) {
                    $response->contents = $this->render(
                        'admin_contact.html.twig',
                        $source->fetch('dev.glu.contact_form.messages')
                    );
                }
            )
        ];
    }

    public function templateDirectories(): array
    {
        return [
            __DIR__ . '/Template'
        ];
    }


}
