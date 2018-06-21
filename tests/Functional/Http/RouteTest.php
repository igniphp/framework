<?php declare(strict_types=1);

namespace IgniTestFunctional\Http;

use Igni\Http\Router\Route;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(Route::class, new Route('test'));
    }

    public function testRouteNaming(): void
    {
        $route = Route::get('/test/{var}');
        self::assertSame('test_var', $route->getName());

        $route = Route::get('/test/{var1}<\d+>');
        self::assertSame('test_var1', $route->getName());

        $route = Route::get('/test/{blabla}', 'test_route');
        self::assertSame('test_route', $route->getName());
    }
}
