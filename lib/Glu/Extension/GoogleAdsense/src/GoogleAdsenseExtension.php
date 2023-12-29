<?php

namespace Glu\Extension\GoogleAdsense;

use Glu\DependencyInjection\Container;
use Glu\Event\Lifecycle\ResponseReadyEvent;
use Glu\Event\Listener;
use Glu\Extension\BaseExtension;
use Glu\Templating\_Function;
use Psr\Container\ContainerInterface;

final class GoogleAdsenseExtension extends BaseExtension
{
    private string $clientId;
    private bool $usingAds;

    public function __construct(
        string $clientId
    )
    {
        $this->clientId = $clientId;
        $this->usingAds = false;
    }

    public static function load(ContainerInterface $container, array $context): static
    {
        return new self($context['client_id']);
    }

    public function name(): string
    {
        return 'glu.ext.google_adsense';
    }

    public function rendererFunctions(): array
    {
        $clientId = $this->clientId;

        return [
            new _Function(
                'adsense_ad',
                function(string $slot, string $format = 'link') use ($clientId) {
                    $this->usingAds = true;
                    return <<<CODE
<ins class="adsbygoogle"
	 style="display:block"
	 data-ad-client="$clientId"
	 data-ad-slot="$slot"
	 data-ad-format="$format"
	 data-full-width-responsive="true"></ins>
<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
</script>
CODE;
                }
            , false)
                ];
    }

    public function listeners(): array
    {
        return [
            new Listener('glu.response_ready', function(ResponseReadyEvent $event) {
                if ($this->usingAds) {
                    $event->response()->contents =
                        preg_replace(
                            '#</head>#',
                            '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client="'.$this->clientId.' crossorigin="anonymous"></script></head>',
                            $event->response()->contents,
                            1
                        );
                }
            })
        ];
    }
}
