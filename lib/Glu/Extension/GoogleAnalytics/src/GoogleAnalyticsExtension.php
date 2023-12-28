<?php

namespace Glu\Extension\GoogleAnalytics\src;

use Glu\DependencyInjection\Container;
use Glu\Event\Lifecycle\ResponseReadyEvent;
use Glu\Event\Listener;
use Glu\Extension\BaseExtension;
use Glu\Templating\TemplateRenderer;

final class GoogleAnalyticsExtension extends BaseExtension
{
    private string $id;
    private TemplateRenderer $renderer;

    public function __construct(
        string $id,
        TemplateRenderer $renderer
    )
    {
        $this->id = $id;
        $this->renderer = $renderer;
    }

    public static function load(Container $locator, array $context): static
    {
        return new self($context['id'], $locator->get('template_renderer'));
    }

    public function name(): string
    {
        return 'glu.ext.google_analytics';
    }

    public function templateDirectories(): array
    {
        return [
            __DIR__ . '/Template'
        ];
    }

    public function listeners(): array
    {
        return [
            new Listener('glu.response_ready', function(ResponseReadyEvent $event) {
                $event->response()->contents =
                    preg_replace(
                        '#</head>#',
                        $this->renderer->render('code.html.twig', $event->request(), [
                            'id' => $this->id
                        ]) . '</head>',
                        $event->response()->contents,
                        1
                    );
            })
        ];
    }
}
