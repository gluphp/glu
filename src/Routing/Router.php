<?php declare(strict_types = 1);

namespace Glu\Routing;

use Glu\Http\Request;

final class Router {
    private array $routes = [];

    public function add(Route $route) {
        $parameters = [];

        $path = $route->path();
        $pathRegex = \preg_quote($path, '#');
        if (\strpos($path, '{')) {
            $pathRegex = \preg_replace_callback('/\\\{([A-Za-z0-9]+)\\\}(.)?/',function($m) use (&$parameters) {
                $parameters[] = $m[1];
                return '(?P<'.$m[1].'>[^'.($m[2]??'/').']++)'.($m[2]??'');
            }, $pathRegex);
        }

        foreach ($route->methods() as $method) {
            if (false === isset($this->routes[$method])) {
                $this->routes[$method] = [];
            }

            $this->routes[$method][$pathRegex] = $route;

            if ($method === 'get') {
                $this->routes['head'][$pathRegex] = $route;
            }
        }
    }

    public function match(Request $request): MatchResult {
        $parameters = [];
        $route = null;
        foreach ($this->routes[$request->method()] ?? [] as $path => $routeInfo) {
            if (0 !== \preg_match('#^'.$path.'$#', $request->path(), $m)) {
                foreach ($routeInfo['parameters'] as $definedParameter) {
                    $parameters[$definedParameter] = $m[$definedParameter];
                }
                $route = $routeInfo;
            }
        }

        if ($route === null) {
            return MatchResult::createNotFound();
        }

        return MatchResult::createFound($route, $parameters);
    }

    public function generate(string $name, array $parameters): string
    {
        foreach ($this->routes as $methodRoutes) {
            foreach ($methodRoutes as $routeRegex=>$route) {
                if ($route['name'] === $name) {
                    $parametersReplace = [];
                    foreach ($parameters as $key => $value) {
                        $parametersReplace['{' . $key . '}'] = $value;
                    }
                    return \strtr($route['path'], $parametersReplace);
                }
            }
        }

        return '#notfound';
    }
}
