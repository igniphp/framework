<?php declare(strict_types=1);

namespace Igni\Http\Router;

use Igni\Http\Exception\MethodNotAllowedException;
use Igni\Http\Exception\NotFoundException;
use Igni\Http\Route;
use Igni\Http\Router as RouterInterface;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\Exception\MethodNotAllowedException as SymfonyMethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Wrapper for package symfony/router
 *
 * @package Igni\Http\Router
 */
class Router implements RouterInterface
{
    /** @var RouteCollection */
    protected $routeCollection;

    /** @var Route[] */
    protected $routes = [];

    public function __construct()
    {
        $this->routeCollection = new RouteCollection();
    }

    /**
     * Registers new route.
     *
     * @param \Igni\Http\Router\Route $route
     */
    public function addRoute(Route $route): void
    {
        $name = $route->getName();

        $baseRoute = new SymfonyRoute($route->getPath());
        $baseRoute->setMethods($route->getMethods());

        $this->routeCollection->add($name, $baseRoute);
        $this->routes[$name] = $route;
    }

    /**
     * Finds route matching clients request.
     *
     * @param string $method request method.
     * @param string $path request path.
     * @return Route
     */
    public function findRoute(string $method, string $path): Route
    {
        $matcher = new UrlMatcher($this->routeCollection, new RequestContext('/', $method));
        try {
            $route = $matcher->match($path);
        } catch (ResourceNotFoundException $exception) {
            throw NotFoundException::notFound($path, $method);
        } catch (SymfonyMethodNotAllowedException $exception) {
            throw MethodNotAllowedException::methodNotAllowed($path, $method, $exception->getAllowedMethods());
        }

        $routeName = $route['_route'];
        unset($route['_route']);

        return $this->routes[$routeName]->withAttributes($route);
    }
}
