<?php declare(strict_types=1);

namespace Igni\Http;

use FastRoute\DataGenerator;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteParser;
use Igni\Http\Exception\MethodNotAllowedException;
use Igni\Http\Exception\NotFoundException;
use Igni\Http\Exception\RouterException;

class Router
{
    private $routeParser;
    private $dataGenerator;

    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator)
    {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
    }

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

    public function findRoute(array $query): Route
    {
        $dispatcher = new GroupCountBased($this->getData());
        $info = $dispatcher->dispatch($query['method'], $query['uri']);

        switch ($info[0]) {
            case Dispatcher::NOT_FOUND:
                throw NotFoundException::notFound($query['uri'], $query['method']);

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $info[1];
                throw MethodNotAllowedException::methodNotAllowed($query['uri'], $query['method'], $allowedMethods);

            case Dispatcher::FOUND:
                return $info[1]->withAttributes($info[2]);
        }
    }

    protected function getData()
    {
        return $this->dataGenerator->getData();
    }
}
