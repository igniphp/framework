<?php declare(strict_types=1);

namespace IgniTest\Funcational\Http\Controller;

use Igni\Http\Controller\ControllerAggregate;
use Igni\Http\Route;
use Igni\Http\Router;
use IgniTest\Fixtures\HttpController;
use PHPUnit\Framework\TestCase;
use Mockery;

class ControllerAggregateTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(
            ControllerAggregate::class,
            new ControllerAggregate(Mockery::mock(Router::class))
        );
    }

    public function testAddCallableController(): void
    {
        $controller = function() {};
        $route = Mockery::mock(Route::class);
        $route->shouldReceive('delegate')
            ->withArgs([$controller]);

        $router = Mockery::mock(Router::class);
        $router->shouldReceive('addRoute')
            ->withArgs([$route]);
        $aggregate = new ControllerAggregate($router);

        self::assertNull($aggregate->add($controller, $route));
    }

    public function testAddControllerClass(): void
    {
        $controller = HttpController::class;

        $router = Mockery::mock(Router::class);
        $router->shouldReceive('addRoute')
            ->withArgs(function($route) {
                self::assertInstanceOf(Route::class, $route);
                self::assertSame(HttpController::URI, $route->getExpression());
                return true;
            });
        $aggregate = new ControllerAggregate($router);

        self::assertNull($aggregate->add($controller));
    }

    public function testAddControllerObject(): void
    {
        $controller = new HttpController();

        $router = Mockery::mock(Router::class);
        $router->shouldReceive('addRoute')
            ->withArgs(function($route) {
                self::assertInstanceOf(Route::class, $route);
                self::assertSame(HttpController::URI, $route->getExpression());
                return true;
            });
        $aggregate = new ControllerAggregate($router);

        self::assertNull($aggregate->add($controller));
    }
}
