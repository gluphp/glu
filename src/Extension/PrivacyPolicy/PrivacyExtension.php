<?php

namespace Glu\Extension\PrivacyPolicy;

use Glu\DependencyInjection\ServiceLocator;
use Glu\Event\Listener;
use Glu\Event\ResponseEvent;
use Glu\Extension\BaseExtension;
use Glu\Http\Request;
use Glu\Http\Response;
use Glu\Routing\Route;
use Glu\Templating\_Function;

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

    public static function load(ServiceLocator $locator, array $context): static
    {
        return new self(
            $context['organization'],
            $context['base_template']
        );
    }

    public function name(): string
    {
        return 'dev.glu.privacy_policy';
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
