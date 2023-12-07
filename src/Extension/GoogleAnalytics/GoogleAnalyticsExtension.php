<?php

namespace Glu\Extension\GoogleAnalytics;

use Glu\DependencyInjection\ServiceLocator;
use Glu\Event\Listener;
use Glu\Event\ResponseEvent;
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

    public static function load(ServiceLocator $locator, array $context): static
    {
        return new self($context['id'], $locator->get('template_renderer'));
    }

    public function name(): string
    {
        return 'dev.glu.google_analytics';
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
            new Listener('glu.response', function(ResponseEvent $event) {
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
