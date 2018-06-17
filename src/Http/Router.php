<?php declare(strict_types=1);

namespace Igni\Http;

use Igni\Http\Exception\MethodNotAllowedException;
use Igni\Http\Exception\NotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException as SymfonyMethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Utility class for simplifying api for nikic router.
 *
 * @package Igni\Http
 */
class Router
{
    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    /** @var array */
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
    public function addRoute(Route $route): void
    {
        $baseRoute = $route->getBaseRoute();
        $this->routeCollection->add($route->getName(), $baseRoute);
        $this->routes[$route->getName()] = $route;
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
