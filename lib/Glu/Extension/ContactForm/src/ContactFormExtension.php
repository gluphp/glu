<?php

namespace Glu\Extension\ContactForm;

use Glu\Adapter\DataSource\FilesystemSource;
use Glu\DataSource\Source;
use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\Parameter;
use Glu\DependencyInjection\Reference;
use Glu\DependencyInjection\Service;
use Glu\Extension\BaseExtension;
use Glu\Extension\ContactForm\Controller\AdminListController;
use Glu\Extension\ContactForm\Controller\ContactFormHandler;
use Glu\Extension\ContactForm\Templating\ContactFormFunction;
use Glu\Extension\GoogleAnalytics\Listener\CodeInjectorListener;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;
use Glu\Templating\ConcreteFunction;
use Psr\Container\ContainerInterface;

final class ContactFormExtension extends BaseExtension
{
    private string $source;
    private string $pathPrefix;
    private string $successPath;

    public function __construct(
        string $source,
        string $pathPrefix = '',
        string $successPath = '/'
    )
    {
        $this->source = $source;
        $this->pathPrefix = $pathPrefix;
        $this->successPath = $successPath;
    }

    public function name(): string
    {
        return 'glu.ext.contact_form';
    }

    public function containerDefinitions(): array
    {
        return [
            new Service(
                'glu.ext.contact_form.controller.contact_form_handler',
                ContactFormHandler::class,
                [
                    new Reference($this->source)
                ]
            ),
            new Service(
                'glu.ext.contact_form.controller.admin_list',
                AdminListController::class,
                [
                    new Reference(Container::SERVICE_TEMPLATING_RENDERER),
                    new Reference($this->source)
                ]
            ),
            new Service(
                'glu.ext.contact_form.templating.function.contact_form',
                ContactFormFunction::class,
                [],
                [Container::TAG_TEMPLATING_FUNCTION]
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
                'glu.ext.contact_form.controller.contact_form_handler'
            ),
            new Route(
                $this->name() . '.routes.admin_list',
                'GET',
                '/admin/contact',
                'glu.ext.contact_form.controller.admin_list'
            )
        ];
    }

    public function configuration(): array
    {
        return [
            __DIR__ . '/Template'
        ];
    }
}
