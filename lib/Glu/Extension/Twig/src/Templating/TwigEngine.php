<?php declare(strict_types = 1);

namespace Glu\Extension\Twig\Templating;

use Glu\Environment;
use Glu\Http\Request;
use Glu\Routing\Router;
use Glu\SessionManagement;
use Glu\Templating\_Function;
use Glu\Templating\Engine;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

final class TwigEngine implements Engine {
    private bool $initialized;
    /** @var string[] */
    private array $directories;
    private TwigEnvironment $twig;
    private Router $router;
    private Environment $environment;
    private ?string $cacheDirectory;
    private array $functions;

    public function __construct(
        array $directories,
        Router $router,
        Environment $environment,
        array $functions,
        ?string $cacheDirectory = null
    ) {
        $this->initialized = false;
        $this->directories = $directories;
        $this->router = $router;
        $this->environment = $environment;
        $this->functions = $functions;
        $this->cacheDirectory = $cacheDirectory;
    }

    private function initialize() {
        if ($this->initialized) {
            return;
        }

        $options = [];
        if ($this->cacheDirectory !== null) {
            $options['cache'] = $this->cacheDirectory . '/twig';
        }
        $this->twig = new TwigEnvironment(new FilesystemLoader($this->directories), $options);
        $this->twig->addFunction(new TwigFunction('asset', function (string $path) {
            return '/' . $path;
        }));
        $this->twig->addFunction(new TwigFunction('path', function (string $name, array $parameters = []) {
            return $this->router->generate($name, $parameters);
        }));
        $this->twig->addFunction(new TwigFunction('is_granted', function (string $role) {
            return false;
        }));
        $this->twig->addFunction(new TwigFunction('dump', function (mixed ...$values) {
            \var_dump($values);
        }));

        foreach ($this->functions as $function) {
            $this->addFunction($function);
        }

        $this->initialized = true;
    }

    public function registerFunction(_Function $function): void
    {
        $this->functions[] = $function;
    }

    private function addFunction(_Function $function): void
    {
        $options = [];
        if ($function->escape() === false) {
            $options['is_safe'] = ['html'];
        }

        $this->twig->addFunction(
            new TwigFunction($function->name(), $function->callable(), $options)
        );
    }

    public function render(string $path, Request $request, array $context = []): string
    {
        $this->initialize();

        return $this->twig->load($path)
            ->render(array_merge($context, [
                'app' => [
                    'user' => SessionManagement::retrieveLoggedInUser()
                ],
                //'session' => $_SESSION,
                'tomato' => [
                    'url' => $request->url(),
                    'request' => $request
                ],
                'global' => $this->environment->all('globals'),
                'env' => $this->environment->get('globals', 'env')
            ]));
    }

    public function registerDirectory(string $directory): void
    {
        $this->directories[] = $directory;
    }

    public function supports(string $path): bool
    {
        return \substr($path, -5) === '.twig';
    }
}
