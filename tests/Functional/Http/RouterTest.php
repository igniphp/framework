<?php declare(strict_types=1);

namespace IgniTestFunctional\Http;

use Igni\Http\Exception\MethodNotAllowedException;
use Igni\Http\Exception\NotFoundException;
use Igni\Http\Route;
use Igni\Http\Router;
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
        $test2 = Route::post('/a[/b[/{c}]]');
        $router = new Router();
        $router->addRoute($test);
        $router->addRoute($test1);
        $router->addRoute($test2);

        $result = $router->findRoute('POST', '/a/b');


        $result = $router->findRoute('POST', '/a/b/1');


        $result = $router->findRoute('GET', '/test');

    }

    public function testNotFound(): void
    {
        $test = Route::get('/test');
        $router = new Router();
        $router->addRoute($test);

        $this->expectException(NotFoundException::class);
        $router->findRoute('GET', '/a/b');
    }

    public function testMethodNotAllowed(): void
    {
        $test = Route::get('/test');
        $router = new Router();
        $router->addRoute($test);

        $this->expectException(MethodNotAllowedException::class);
        $router->findRoute('POST', '/test');
    }
}
