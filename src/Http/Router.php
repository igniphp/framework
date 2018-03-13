<?php declare(strict_types=1);

namespace Igni\Http;

use FastRoute\DataGenerator;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteParser;
use Igni\Http\Exception\MethodNotAllowedException;
use Igni\Http\Exception\NotFoundException;
use Igni\Http\Exception\RouterException;

/**
 * Utility class for simplifying api for nikic router.
 *
 * @package Igni\Http
 */
class Router
{
    /**
     * @var RouteParser
     */
    private $routeParser;

    /**
     * @var DataGenerator
     */
    private $dataGenerator;

    /**
     * Router constructor.
     * @param RouteParser $routeParser
     * @param DataGenerator $dataGenerator
     */
    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator)
    {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
    }

    /**
     * Registers new route.
     *
     * @param Route $route
     */
    public function addRoute(Route $route): void
    {
        if (!$route instanceof Route) {
            throw RouterException::invalidRoute($route);
        }
        $routeDatas = $this->routeParser->parse($route->getExpression());
        foreach ((array) $route->getMethod() as $method) {
            foreach ($routeDatas as $routeData) {
                $this->dataGenerator->addRoute($method, $routeData, $route);
            }
        }
    }

    /**
     * Finds route matching clients request.
     *
     * @param string $method request method.
     * @param string $uri request uri.
     * @return Route
     */
    public function findRoute(string $method, string $uri): Route
    {
        $dispatcher = new GroupCountBased($this->getData());
        $info = $dispatcher->dispatch($method, $uri);

        switch ($info[0]) {
            case Dispatcher::NOT_FOUND:
                throw NotFoundException::notFound($uri, $method);

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $info[1];
                throw MethodNotAllowedException::methodNotAllowed($uri, $method, $allowedMethods);

            case Dispatcher::FOUND:
                return $info[1]->withAttributes($info[2]);
        }
    }

    protected function getData()
    {
        return $this->dataGenerator->getData();
    }
}
