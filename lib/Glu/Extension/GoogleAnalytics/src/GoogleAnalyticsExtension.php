<?php

namespace Glu\Extension\GoogleAnalytics;

use Glu\DependencyInjection\Container;
use Glu\Event\Lifecycle\ResponseReadyEvent;
use Glu\Event\Listener;
use Glu\Extension\BaseExtension;
use Glu\Templating\Engine;
use Glu\Templating\Renderer;
use Psr\Container\ContainerInterface;

final class GoogleAnalyticsExtension extends BaseExtension
{
    private string $id;
    private Renderer $renderer;

    public function __construct(
        string $id,
        Renderer $renderer
    )
    {
        $this->id = $id;
        $this->renderer = $renderer;
    }

    public static function load(ContainerInterface $container, array $context): static
    {
        return new self($context['id'], $container->get('glu.templating.renderer'));
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
                $event->response()->replace(
                    '#</head>#',
                    $this->renderer->render('code.html.twig', $event->request(), [
                        'id' => $this->id
                    ]) . '</head>',
                    1
                );
            })
        ];
    }
}
