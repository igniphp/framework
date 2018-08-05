<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Http;

use Igni\Http\Exception\GenericHttpException;
use Igni\Http\Router\Route;
use Igni\Http\Router\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(
            Router::class,
            new Router()
        );
    }

    public function testAddRoute(): void
    {
        $route = Route::get('/test');
        $router = new Router();
        $router->addRoute($route);

        self::assertInstanceOf(Router::class, $router);
    }

    public function testFindRoute(): void
    {
        $test = Route::get('/test');
        $test1 = Route::get('/test/{id}');
        $test2 = Route::post('/a/{b?b}/{c?c}');
        $router = new Router();
        $router->addRoute($test);
        $router->addRoute($test1);
        $router->addRoute($test2);

        $result = $router->findRoute('POST', '/a/1');
        self::assertInstanceOf(Route::class, $result);
        self::assertSame(['b' => '1', 'c' => 'c'], $result->getAttributes());
        $result = $router->findRoute('POST', '/a/1/1');
        self::assertInstanceOf(Route::class, $result);
        self::assertSame(['b' => '1', 'c' => '1'], $result->getAttributes());
        $result = $router->findRoute('GET', '/test');
        self::assertInstanceOf(Route::class, $result);
        self::assertSame([], $result->getAttributes());
    }

    public function testNotFound(): void
    {
        $test = Route::get('/test');
        $router = new Router();
        $router->addRoute($test);

        $this->expectException(GenericHttpException::class);
        $router->findRoute('GET', '/a/b');
    }

    public function testMethodNotAllowed(): void
    {
        $test = Route::get('/test');
        $router = new Router();
        $router->addRoute($test);

        $this->expectException(GenericHttpException::class);
        $router->findRoute('POST', '/test');
    }

    public function testMatchOptionals(): void
    {
        $test = Route::delete('/users/{name<\d+>?2}');
        $router = new Router();
        $router->addRoute($test);

        $route = $router->findRoute('DELETE', '/users');
        self::assertSame('2', $route->getAttribute('name'));

        $route = $router->findRoute('DELETE', '/users/1');
        self::assertSame('1', $route->getAttribute('name'));
    }
}
