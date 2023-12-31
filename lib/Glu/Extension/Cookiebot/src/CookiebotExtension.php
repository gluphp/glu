<?php

namespace Glu\Extension\Cookiebot;

use Glu\DependencyInjection\Container;
use Glu\DependencyInjection\Parameter;
use Glu\DependencyInjection\Service;
use Glu\Extension\BaseExtension;
use Glu\Extension\Cookiebot\Listener\CodeInjectorListener;
use Glu\Extension\Cookiebot\Templating\CookieDeclarationFunction;

final class CookiebotExtension extends BaseExtension
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function name(): string
    {
        return 'glu.ext.cookiebot';
    }

    public function containerDefinitions(): array
    {
        return [
            new Parameter('glu.ext.cookiebot.id', $this->id),
            new Service(
                'glu.ext.cookiebot.templating.function.cookie_declaration',
                CookieDeclarationFunction::class,
                [
                    'glu.ext.cookiebot.id'
                ],
                [Container::TAG_TEMPLATING_FUNCTION]
            ),
            new Service(
                'glu.ext.cookiebot.listener.code_injector',
                CodeInjectorListener::class,
                [
                    'glu.ext.cookiebot.id'
                ],
                [Container::TAG_LISTENER]
            ),
        ];
    }
}
