<?php

namespace Glu\Extension\GoogleAdsense;

use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\Parameter;
use Glu\DependencyInjection\Service;
use Glu\Extension\BaseExtension;
use Glu\Extension\Cookiebot\Listener\CodeInjectorListener;
use Glu\Extension\GoogleAdsense\Templating\AdFunction;

final class GoogleAdsenseExtension extends BaseExtension
{
    private string $clientId;

    public function __construct(
        string $clientId
    ) {
        $this->clientId = $clientId;
    }

    public function name(): string
    {
        return 'glu.ext.google_adsense';
    }

    public function containerDefinitions(): array
    {
        return [
            new Parameter('glu.ext.google_adsense.id', $this->clientId),
            new Service(
                'glu.ext.google_adsense.templating.function.ad',
                AdFunction::class,
                [
                    'glu.ext.google_adsense.id'
                ],
                [Container::TAG_TEMPLATING_FUNCTION]
            ),
            new Service(
                'glu.ext.google_adsense.listener.code_injector',
                CodeInjectorListener::class,
                [
                    'glu.ext.google_adsense.id'
                ],
                [Container::TAG_LISTENER]
            ),
        ];
    }
}
