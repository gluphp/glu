<?php

namespace Glu\Extension\ContactForm;

use Glu\Adapter\DataSource\FilesystemSource;
use Glu\DataSource\Source;
use Glu\DependencyInjection\Container;
use Glu\Extension\BaseExtension;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;
use Glu\Templating\_Function;
use Psr\Container\ContainerInterface;

final class ContactFormExtension extends BaseExtension
{
    private Source $source;
    private string $pathPrefix;
    private string $successPath;

    public function __construct(
        Source $source,
        string $pathPrefix,
        string $successPath
    )
    {
        $this->source = $source;
        $this->pathPrefix = $pathPrefix;
        $this->successPath = $successPath;
    }

    public static function load(ContainerInterface $container, array $context): static
    {
        return new self(
            $context['source'] ?? new FilesystemSource(
                $container->get('data_directory') . '/glu/contact_form'
            ),
                $context['path_prefix'] ?? '',
                $context['success_path'] ?? '/',
        );
    }

    public function name(): string
    {
        return 'glu.ext.contact_form';
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
                $this->name() . '.routes.handler',
                ['GET', 'POST'],
                $this->pathPrefix . '/contact-handle',
                function (Request $request, Response $response, array $args) use ($source) {
                    if ($request->method() === 'POST') {
                        $source->insert('dev.glu.contact_form.messages', [
                            'email' => $request->form('email'),
                            'message' => $request->form('message')
                        ]);

                        $response->statusCode = 301;
                        $response->headers['location'] = $this->pathPrefix . $this->successPath;
                    }
                }
            ),
            new Route(
                $this->name() . '.routes.admin_list',
                'GET',
                '/admin/contact',
                function (Request $request, Response $response, array $args) use ($source) {
                    $response->contents = $this->render(
                        'admin_contact.html.twig',
                        [
                            'items' => $source->fetch('dev.glu.contact_form.messages')
                        ]
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
