<?php

namespace Glu\Extension\GoogleAnalytics;

use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\Parameter;
use Glu\DependencyInjection\Service;
use Glu\Extension\BaseExtension;
use Glu\Extension\GoogleAnalytics\Listener\CodeInjectorListener;

final class GoogleAnalyticsExtension extends BaseExtension
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function name(): string
    {
        return 'glu.ext.google_analytics';
    }

    public function configuration(): array
    {
        return [
            'template_dir' => __DIR__ . '/Template'
        ];
    }

    public function containerDefinitions(): array
    {
        return [
            new Parameter('glu.ext.google_analytics.id', $this->id),
            new Service(
                'glu.ext.google_analytics.listener.code_injector',
                CodeInjectorListener::class,
                [
                    Container::SERVICE_TEMPLATING_RENDERER,
                    'glu.ext.google_analytics.id'
                ],
                [Container::TAG_LISTENER]
            )
        ];
    }
}
