<?php

declare(strict_types=1);

namespace Glu\Routing;

use Glu\Http\Request;

final class Router
{
    /** @var array<string, CompiledRoute[]> */
    private array $routes = [];

    public function add(Route $route)
    {
        $parameters = [];

        $path = $route->path();
        $pathRegex = \preg_quote($path, '#');
        if (\strpos($path, '{')) {
            $pathRegex = \preg_replace_callback('/\\\{([A-Za-z0-9]+)\\\}(.)?/', function ($m) use (&$parameters) {
                $parameters[] = $m[1];
                return '(?P<'.$m[1].'>[^'.($m[2] ?? '/').']++)'.($m[2] ?? '');
            }, $pathRegex);
        }

        foreach ($route->methods() as $method) {
            if (false === isset($this->routes[$method])) {
                $this->routes[$method] = [];
            }

            $compiledRoute = new CompiledRoute($route, $pathRegex, $parameters);
            $this->routes[$method][] = $compiledRoute;

            if ($method === 'get') {
                $this->routes['head'][] = $compiledRoute;
            }
        }
    }

    public function match(Request $request): MatchResult
    {
        $parameters = [];
        $match = null;
        foreach ($this->routes[$request->method()] ?? [] as $route) {
            if (0 !== \preg_match('#^'.$route->regex().'$#', $request->path(), $m)) {
                foreach ($route->parameters() as $definedParameter) {
                    $parameters[$definedParameter] = $m[$definedParameter];
                }
                $match = $route;
            }
        }

        if ($match === null) {
            return MatchResult::createNotFound();
        }

        return MatchResult::createFound($match->route(), $parameters);
    }

    public function generate(string $name, array $parameters): string
    {
        foreach ($this->routes as $methodRoutes) {
            foreach ($methodRoutes as $route) {
                if ($route->route()->name() === $name) {
                    $parametersReplace = [];
                    foreach ($parameters as $key => $value) {
                        $parametersReplace['{' . $key . '}'] = $value;
                    }
                    return \strtr($route->route()->path(), $parametersReplace);
                }
            }
        }

        return '#notfound';
    }
}
