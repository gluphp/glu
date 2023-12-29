<?php

namespace Glu\Extension\Cookiebot\Templating;

use Glu\Templating\_Function;

final class CookieDeclarationFunction implements _Function
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function name(): string
    {
        return 'cookieDeclaration';
    }

    public function callable(): callable
    {
        return '<script id="CookieDeclaration" src="https://consent.cookiebot.com/'.$this->id.'/cd.js" type="text/javascript" async></script>';
    }

    public function escape(): bool
    {
        return false;
    }

}
