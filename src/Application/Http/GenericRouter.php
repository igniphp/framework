<?php declare(strict_types=1);

namespace Igni\Application\Http;

use Igni\Network\Http\Router;
use Igni\Network\Exception\RouterException;
use Igni\Network\Http\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException as SymfonyMethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

/**
 * Wrapper for package symfony/router
 *
 * @package Igni\Http\Router
 */
class GenericRouter implements Router
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
     * @param Route $route
     */
    public function add(Route $route): void
    {
        if ($route instanceof Route) {
            $name = $route->getName();
        } else {
            $name = Route::generateNameFromPath($route->getPath());
        }

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
    public function find(string $method, string $path): Route
    {
        $matcher = new UrlMatcher($this->routeCollection, new RequestContext('/', $method));
        try {
            $route = $matcher->match($path);
        } catch (ResourceNotFoundException $exception) {
            throw RouterException::noRouteMatchesRequestedUri($path, $method);
        } catch (SymfonyMethodNotAllowedException $exception) {
            throw RouterException::methodNotAllowed($path, $exception->getAllowedMethods());
        }

        $routeName = $route['_route'];
        unset($route['_route']);

        return $this->routes[$routeName]->withAttributes($route);
    }
}
