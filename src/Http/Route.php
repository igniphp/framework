<?php declare(strict_types=1);

namespace Igni\Http;

/**
 * Represents route pattern.
 *
 * @package Igni\Http
 */
class Route
{
    /**
     * @var string
     */
    private $expression;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * Callable or valid class name
     * @var callable|string
     */
    private $handler;

    /**
     * Route constructor.
     *
     * @param string $expression
     * @param string $method
     */
    public function __construct(string $expression, string $method)
    {
        $this->expression = $expression;
        $this->method = $method;
    }

    /**
     * Sets controller that is going to handle all request
     * which uri matches the route pattern.
     *
     * @param $handler
     * @return $this
     */
    public function delegate($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Returns route pattern.
     *
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * Returns request method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns controller that listen to request
     * which uri matches the route pattern.
     *
     * @return callable|string
     */
    public function getDelegator()
    {
        return $this->handler;
    }

    /**
     * Factories new instance of the current route with
     * attributes retrieved from client's request.
     *
     * @param array $attributes
     * @return Route
     */
    public function withAttributes(array $attributes): Route
    {
        $route = clone $this;
        $route->attributes = $attributes;

        return $route;
    }

    /**
     * Returns attributes extracted from the uri.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Factories new instance of the route
     * that will be matched against get request.
     *
     * @param string $expression
     * @return Route
     */
    public static function get(string $expression): Route
    {
        return new self($expression, Request::METHOD_GET);
    }

    /**
     * Factories new instance of the route
     * that will be matched against post request.
     *
     * @param string $expression
     * @return Route
     */
    public static function post(string $expression): Route
    {
        return new self($expression, Request::METHOD_POST);
    }

    /**
     * Factories new instance of the route
     * that will be matched against put request.
     *
     * @param string $expression
     * @return Route
     */
    public static function put(string $expression): Route
    {
        return new self($expression, Request::METHOD_PUT);
    }

    /**
     * Factories new instance of the route
     * that will be matched against delete request.
     *
     * @param string $expression
     * @return Route
     */
    public static function delete(string $expression): Route
    {
        return new self($expression, Request::METHOD_DELETE);
    }

    /**
     * Factories new instance of the route
     * that will be matched against patch request.
     *
     * @param string $expression
     * @return Route
     */
    public static function patch(string $expression): Route
    {
        return new self($expression, Request::METHOD_PATCH);
    }

    /**
     * Factories new instance of the route
     * that will be matched against head request.
     *
     * @param string $expression
     * @return Route
     */
    public static function head(string $expression): Route
    {
        return new self($expression, Request::METHOD_HEAD);
    }

    /**
     * Factories new instance of the route
     * that will be matched against options request.
     *
     * @param string $expression
     * @return Route
     */
    public static function options(string $expression): Route
    {
        return new self($expression, Request::METHOD_OPTIONS);
    }
}
