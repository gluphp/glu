<?php

namespace Glu\Extension\GoogleAdsense;

use Glu\DependencyInjection\ServiceLocator;
use Glu\Event\Listener;
use Glu\Event\ResponseEvent;
use Glu\Extension\BaseExtension;
use Glu\Templating\_Function;

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

    public static function load(ServiceLocator $locator, array $context): self
    {
        return new self($context['client_id']);
    }

    public function name(): string
    {
        return 'dev.glu.google_adsense';
    }

    public function rendererFunctions(): array
    {
        $clientId = $this->clientId;

        return [
            new _Function(
                'adsense:ad',
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
            )
                ];
    }

    public function listeners(): array
    {
        return [
            new Listener('glu.response', function(ResponseEvent $event) {
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
