<?php

namespace Glu\Extension\Cookiebot;

use Glu\DependencyInjection\Container;
use Glu\Event\Lifecycle\ResponseReadyEvent;
use Glu\Event\Listener;
use Glu\Extension\BaseExtension;
use Psr\Container\ContainerInterface;

final class CookiebotExtension extends BaseExtension
{
    private string $id;

    public function __construct(
        string $id
    )
    {
        $this->id = $id;
    }

    public static function load(ContainerInterface $container, array $context): static
    {
        return new self($context['id']);
    }

    public function name(): string
    {
        return 'glu.ext.cookiebot';
    }

    public function rendererFunctions(): array
    {
        $id = $this->id;

        return [
            'cookiebot.cookieDeclaration' => function() use ($id) {
                return '<script id="CookieDeclaration" src="https://consent.cookiebot.com/'.$id.'/cd.js" type="text/javascript" async></script>';
            }
        ];
    }

    public function listeners(): array
    {
        return [
            new Listener('glu.response_ready', function(ResponseReadyEvent $event) {
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
