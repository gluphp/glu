<?php declare(strict_types = 1);

namespace Glu\Tests\Routing;

use Glu\Http\RequestBuilder;
use Glu\Routing\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function test_defined_route_is_found(): void
    {
        $router = new Router();
        $router->add('test', 'GET', '/test', function(){});
        $request = (new RequestBuilder('GET', '/test'))
            ->create();

        $routeMatch = $router->match($request);

        self::assertTrue($routeMatch->found);
    }

    public function test_undefined_method_returns_not_found_route_match(): void
    {
        $router = new Router();
        $request = (new RequestBuilder('TEST'))
            ->create();

        $routeMatch = $router->match($request);

        self::assertFalse($routeMatch->found);
    }
}
