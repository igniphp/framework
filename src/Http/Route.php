<?php declare(strict_types=1);

namespace Igni\Http;

class Route
{
    /** @var string */
    private $expression;

    /** @var string */
    private $method;

    /** @var array */
    private $attributes = [];

    /**
     * Callable or valid class name
     * @var callable|string
     */
    private $handler;

    public function __construct(string $expression, string $method)
    {
        $this->expression = $expression;
        $this->method = $method;
    }

    public function delegate($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getDelegator()
    {
        return $this->handler;
    }

    public function withAttributes(array $attributes): Route
    {
        $route = clone $this;
        $route->attributes = $attributes;
        return $route;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public static function get(string $expression): Route
    {
        return new self($expression, Request::METHOD_GET);
    }

    public static function post(string $expression): Route
    {
        return new self($expression, Request::METHOD_POST);
    }

    public static function put(string $expression): Route
    {
        return new self($expression, Request::METHOD_PUT);
    }

    public static function delete(string $expression): Route
    {
        return new self($expression, Request::METHOD_DELETE);
    }

    public static function patch(string $expression): Route
    {
        return new self($expression, Request::METHOD_PATCH);
    }

    public static function head(string $expression): Route
    {
        return new self($expression, Request::METHOD_HEAD);
    }

    public static function options(string $expression): Route
    {
        return new self($expression, Request::METHOD_OPTIONS);
    }
}
