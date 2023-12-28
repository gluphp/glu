<?php


use Glu\DependencyInjection\Container;
use Glu\Extension\BaseExtension;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;

final class TwigExtension extends BaseExtension
{

    public function __construct()
    {
    }

    public static function load(Container $locator, array $context): static
    {
        return new self();
    }

    public function name(): string
    {
        return 'dev.glu.twig';
    }
}
