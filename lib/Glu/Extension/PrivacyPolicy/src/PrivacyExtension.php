<?php

namespace Glu\Extension\PrivacyPolicy\src;

use Glu\DependencyInjection\Container;
use Glu\Extension\BaseExtension;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;

final class PrivacyExtension extends BaseExtension
{
    private string $organization;
    private string $baseTemplate;

    public function __construct(
        string $organization,
        string $baseTemplate
    )
    {
        $this->organization = $organization;
        $this->baseTemplate = $baseTemplate;
    }

    public static function load(Container $locator, array $context): static
    {
        return new self(
            $context['organization'],
            $context['base_template']
        );
    }

    public function name(): string
    {
        return 'glu.ext.privacy_policy';
    }

    public function routes(): array
    {
        $organization = $this->organization;
        $baseTemplate = $this->baseTemplate;

        return [
            new Route(
                'dev.glu.privacy_policy.routes.privacy',
                'GET',
                '/privacy',
                function (Request $request, Response $response, array $args) use ($organization, $baseTemplate) {
                    $response->contents = $this->render('privacy_policy.html.twig', [
                        'organization' => $organization,
                        'base_template' => $baseTemplate
                    ]);
                }
            )
        ];
    }

    public function templateDirectories(): array
    {
        return [
            __DIR__ . '/Template'
        ];
    }
}
