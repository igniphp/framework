<?php declare(strict_types=1);

namespace Igni\Http\Router;

use Igni\Http\Controller;
use Igni\Http\Request;
use Igni\Http\Route as RouteInterface;

/**
 * Proxy class for symfony's route.
 *
 * @package Igni\Http\Router
 */
class Route implements RouteInterface
{
    /** @var string */
    private $name;

    /** @var array */
    private $attributes = [];

    /** @var array */
    private $methods = [];

    /** @var string */
    private $path;

    /** @var callable|Controller */
    private $controller;

    /**
     * Route constructor.
     *
     * @param string $path
     * @param string $name
     * @param array $methods
     */
    public function __construct(string $path, array $methods = ['GET'], string $name = '')
    {
        if (empty($name)) {
            $name = self::generateNameFromPath($path);
        }
        $this->name = $name;
        $this->methods = $methods;
        $this->path = $path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withController($controller): RouteInterface
    {
        $instance = clone $this;
        $instance->controller = $controller;

        return $instance;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function withMethods(array $methods): RouteInterface
    {
        $instance = clone $this;
        $instance->methods = $methods;

        return $instance;
    }

    /**
     * Returns route pattern.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns request methods.
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Factories new instance of the current route with
     * attributes retrieved from client's request.
     *
     * @param array $attributes
     * @return Route
     */
    public function withAttributes(array $attributes): RouteInterface
    {
        $instance = clone $this;
        $instance->attributes = $attributes;

        return $instance;
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

    public function getAttribute(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Factories new instance of the route
     * that will be matched against get request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function get(string $path, string $name = ''): Route
    {
        return new self($path, [Request::METHOD_GET], $name);
    }

    /**
     * Factories new instance of the route
     * that will be matched against post request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function post(string $path, string $name = ''): Route
    {
        return new self($path, [Request::METHOD_POST], $name);
    }

    /**
     * Factories new instance of the route
     * that will be matched against put request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function put(string $path, string $name = ''): Route
    {
        return new self($path, [Request::METHOD_PUT], $name);
    }

    /**
     * Factories new instance of the route
     * that will be matched against delete request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function delete(string $path, string $name = ''): Route
    {
        return new self($path, [Request::METHOD_DELETE], $name);
    }

    /**
     * Factories new instance of the route
     * that will be matched against patch request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function patch(string $path, string $name = ''): Route
    {
        return new self($path, [Request::METHOD_PATCH], $name);
    }

    /**
     * Factories new instance of the route
     * that will be matched against head request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function head(string $path, string $name = ''): Route
    {
        return new self($path, [Request::METHOD_HEAD, Request::METHOD_GET], $name);
    }

    /**
     * Factories new instance of the route
     * that will be matched against options request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function options(string $path, string $name = ''): Route
    {
        return new self($path, [Request::METHOD_OPTIONS], $name);
    }

    /**
     * Generates default name from given path expression,
     * /some/{resource} becomes some_resource
     * @param string $path
     * @return string
     */
    private static function generateNameFromPath(string $path): string
    {
        $path = preg_replace('/<[^>]+>/', '', $path);
        return str_replace(['{', '}', '?', '.', '/'], ['', '', '', '_', '_'], trim($path, '/'));
    }
}
