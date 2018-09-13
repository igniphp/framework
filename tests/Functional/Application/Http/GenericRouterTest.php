<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Http;

use Igni\Application\Http\GenericRouter;
use Igni\Network\Exception\RouterException;
use Igni\Network\Http\Route;
use Igni\Network\Http\Router;
use PHPUnit\Framework\TestCase;

final class GenericRouterTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(
            Router::class,
            new GenericRouter()
        );
    }

    public function testAddRoute(): void
    {
        $route = Route::get('/test');
        $router = new GenericRouter();
        $router->add($route);

        self::assertInstanceOf(Router::class, $router);
    }

    public function testFindRoute(): void
    {
        $test = Route::get('/test');
        $test1 = Route::get('/test/{id}');
        $test2 = Route::post('/a/{b?b}/{c?c}');
        $router = new GenericRouter();
        $router->add($test);
        $router->add($test2);
        $router->add($test1);

        $result = $router->find('POST', '/a/1');
        self::assertInstanceOf(Route::class, $result);
        self::assertSame(['b' => '1', 'c' => 'c'], $result->getAttributes());
        $result = $router->find('POST', '/a/1/1');
        self::assertInstanceOf(Route::class, $result);
        self::assertSame(['b' => '1', 'c' => '1'], $result->getAttributes());
        $result = $router->find('GET', '/test');
        self::assertInstanceOf(Route::class, $result);
        self::assertSame([], $result->getAttributes());
    }

    public function testNotFound(): void
    {
        $test = Route::get('/test');
        $router = new GenericRouter();
        $router->add($test);

        $this->expectException(RouterException::class);
        $router->find('GET', '/a/b');
    }

    public function testMethodNotAllowed(): void
    {
        $test = Route::get('/test');
        $router = new GenericRouter();
        $router->add($test);

        $this->expectException(RouterException::class);
        $router->find('POST', '/test');
    }

    public function testMatchOptionals(): void
    {
        $test = Route::delete('/users/{name<\d+>?2}');
        $router = new GenericRouter();
        $router->add($test);

        $route = $router->find('DELETE', '/users');
        self::assertSame('2', $route->getAttribute('name'));

        $route = $router->find('DELETE', '/users/1');
        self::assertSame('1', $route->getAttribute('name'));
    }
}
