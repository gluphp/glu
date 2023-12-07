<?php declare(strict_types = 1);

namespace Glu\Routing;

use Glu\Http\Request;

final class Router {
    private array $routes = [];

    public function add(
        string   $name,
        string   $method,
        string   $path,
        callable|string $controller,
        ?string  $secured = null
    ) {
        $parameters = [];

        $pathRegex = \preg_quote($path, '#');
        if (\strpos($path, '{')) {
            $pathRegex = \preg_replace_callback('/\\\{([A-Za-z0-9]+)\\\}(.)?/',function($m) use (&$parameters) {
                $parameters[] = $m[1];
                return '(?P<'.$m[1].'>[^'.($m[2]??'/').']++)'.($m[2]??'');
            }, $pathRegex);
        }
        if (false === isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$pathRegex] = [
            'name' => $name,
            'path' => $path,
            'method' => $method,
            'controller' => $controller,
            'secured' => $secured,
            'parameters' => $parameters
        ];

        if ($method === 'GET') {
            $this->routes['HEAD'][$pathRegex] = [
                'name' => $name,
                'path' => $path,
                'method' => $method,
                'controller' => $controller,
                'secured' => $secured,
                'parameters' => $parameters
            ];
        }
    }

    public function match(Request $request): RouteMatch {
        $parameters = [];
        $route = null;
        foreach ($this->routes[$request->method] ?? [] as $path => $routeInfo) {
            if (0 !== \preg_match('#^'.$path.'$#', $request->path, $m)) {
                foreach ($routeInfo['parameters'] as $definedParameter) {
                    $parameters[$definedParameter] = $m[$definedParameter];
                }
                $route = $routeInfo;
            }
        }

        if ($route === null) {
            return new RouteMatch(false);
        }

        return new RouteMatch(true, $route['controller'], $parameters, $route['secured']);
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
