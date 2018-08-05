<?php declare(strict_types=1);

namespace Igni\Http\Controller;

use Igni\Application\Controller\ControllerAggregate as ControllerAggregateInterface;
use Igni\Application\Exception\ApplicationException;
use Igni\Http\Controller;
use Igni\Http\Router\Route;
use Igni\Http\Router\Router;

/**
 * Http application's controller aggregate.
 * @see \Igni\Application\Controller
 * @package Igni\Http\Controller
 */
class ControllerAggregate implements ControllerAggregateInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * ControllerAggregate constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Registers new controller and attaches it to passed route.
     *
     * @param callable|string $controller can be either callable or class implementing Igni\Http\Controller interface or its instance.
     * @param Route|null $route must be passed if callable is passed as a controller.
     *
     * @throws ApplicationException if passed controller is not valid.
     */
    public function add($controller, Route $route = null): void
    {
        if (is_callable($controller) && $route !== null) {
            $route = $route->withController($controller);
            $this->router->addRoute($route);
            return;
        }

        if ($controller instanceof Controller) {
            /** @var Route $route */
            $route = $controller::getRoute();
            $route = $route->withController($controller);
            $this->router->addRoute($route);
            return;
        }

        if (is_string($controller) &&
            class_exists($controller) &&
            in_array(Controller::class, class_implements($controller))
        ) {
            /** @var Route $route */
            $route = $controller::getRoute();
            $route = $route->withController($controller);
            $this->router->addRoute($route);
            return;
        }

        throw ApplicationException::forInvalidController($controller);
    }
}
