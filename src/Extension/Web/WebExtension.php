<?php

namespace Glu\Extension\Web;

use ParagonIE\ConstantTime\Base64UrlSafe;
use Glu\DataSource\Source;
use Glu\DependencyInjection\ServiceLocator;
use Glu\Event\Event;
use Glu\Event\Listener;
use Glu\Event\ResponseEvent;
use Glu\Extension\BaseExtension;
use Glu\Extension\User\LoggedInUser;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\In;
use Glu\Routing\Route;
use Glu\SessionManagement;
use Glu\Templating\Template;
use Glu\Templating\TemplateRenderer;
use Glu\App;

final class WebExtension extends BaseExtension
{
    public function __construct(
    )
    {
    }

    public static function load(ServiceLocator $locator, array $context): self
    {
        return new self();
    }

    public function name(): string
    {
        return 'dev.glu.web';
    }

    public function rendererFunctions(): array
    {
        return [
            'web.favicon' => function() {
                return '<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">';
            },
            ''
        ];
    }

    public function listeners(): array
    {
        return [
            new Listener('glu.response', function(ResponseEvent $event) {
                $event->response()->contents =
                    preg_replace(
                        '#<head>#',
                        '<head>'."\n".'<script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="83117f87-afe2-43e1-969b-a0baa6941c6b" data-blockingmode="auto" type="text/javascript"></script>',
                        $event->response()->contents,
                        1
                    );
            })
        ];
    }
}
