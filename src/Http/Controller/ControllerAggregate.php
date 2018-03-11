<?php declare(strict_types=1);

namespace Igni\Http\Controller;

use Igni\Application\Controller\ControllerAggregate as ControllerAggregateInterface;
use Igni\Application\Exception\ApplicationException;
use Igni\Http\Controller;
use Igni\Http\Route;
use Igni\Http\Router;

class ControllerAggregate implements ControllerAggregateInterface
{
    /** @var Router */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function add($controller, Route $route = null): void
    {
        if (is_callable($controller) && $route !== null) {
            $route->delegate($controller);
            $this->router->addRoute($route);
            return;
        }

        if (is_a($controller, Controller::class)) {
            /** @var Route $route */
            $route = $controller::getRoute();
            $route->delegate($controller);
            $this->router->addRoute($route);
            return;
        }

        throw ApplicationException::forInvalidController($controller);
    }
}
