<?php declare(strict_types=1);

namespace Igni\Http;

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
    private $routes;

    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    /**
     * Registers new route.
     *
     * @param Route $route
     */
    public function addRoute(Route $route): void
    {
        $baseRoute = $route->getBaseRoute();
        $this->routes->add($route->getName(), $baseRoute);
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
        $matcher = new UrlMatcher($this->routes, new RequestContext('/', $method));
        $matcher->match($path);
    }
}
