<?php declare(strict_types=1);

namespace IgniTestFunctional\Http;

use FastRoute\DataGenerator\GroupCountBased as StandardDataGenerator;
use FastRoute\RouteParser\Std as StandardRouteParser;
use FastRoute\DataGenerator;
use FastRoute\RouteParser;
use Igni\Http\Exception\MethodNotAllowedException;
use Igni\Http\Exception\NotFoundException;
use Igni\Http\Route;
use Igni\Http\Router;
use Igni\Utils\TestCase;

class RouterTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(
            Router::class,
            new Router(self::mock(RouteParser::class), self::mock(DataGenerator::class))
        );
    }

    public function testAddRoute(): void
    {
        $route = Route::get('/test');
        $routeParser = self::mock(RouteParser::class);
        $routeParser
            ->shouldReceive('parse')
            ->withArgs(['/test'])
            ->andReturn(['/test']);

        $dataGenerator = self::mock(DataGenerator::class);
        $dataGenerator
            ->shouldReceive('addRoute')
            ->withArgs(['GET', '/test', $route]);

        $router = new Router($routeParser, $dataGenerator);
        $router->addRoute($route);

        self::assertInstanceOf(Router::class, $router);
    }

    public function testFindRoute(): void
    {
        $test = Route::get('/test');
        $test1 = Route::get('/test/{id}');
        $test2 = Route::post('/a[/b[/{c}]]');
        $router = new Router(new StandardRouteParser(), new StandardDataGenerator());
        $router->addRoute($test);
        $router->addRoute($test1);
        $router->addRoute($test2);

        $result = $router->findRoute('POST', '/a/b');
        self::assertNotSame($test2, $result);
        self::assertSame($test2->getMethod(), $result->getMethod());
        self::assertSame($test2->getExpression(), $result->getExpression());

        $result = $router->findRoute('POST', '/a/b/1');
        self::assertNotSame($test2, $result);
        self::assertSame($test2->getMethod(), $result->getMethod());
        self::assertSame($test2->getExpression(), $result->getExpression());
        self::assertEquals('1', $result->getAttributes()['c']);

        $result = $router->findRoute('GET', '/test');
        self::assertNotSame($test, $result);
        self::assertSame($test->getMethod(), $result->getMethod());
        self::assertSame($test->getExpression(), $result->getExpression());
    }

    public function testNotFound(): void
    {
        $test = Route::get('/test');
        $router = new Router(new StandardRouteParser(), new StandardDataGenerator());
        $router->addRoute($test);

        $this->expectException(NotFoundException::class);
        $router->findRoute('GET', '/a/b');
    }

    public function testMethodNotAllowed(): void
    {
        $test = Route::get('/test');
        $router = new Router(new StandardRouteParser(), new StandardDataGenerator());
        $router->addRoute($test);

        $this->expectException(MethodNotAllowedException::class);
        $router->findRoute('POST', '/test');
    }
}
