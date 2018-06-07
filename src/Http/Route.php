<?php declare(strict_types=1);

namespace Igni\Http;

use Symfony\Component\Routing\Route as BaseRoute;

/**
 * Proxy class for symfony's route.
 *
 * @package Igni\Http
 */
class Route
{
    /**
     * @var BaseRoute
     */
    private $baseRoute;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $requirements = [];

    /**
     * @var array
     */
    private $defaults = [];

    /**
     * Route constructor.
     *
     * @param string $path
     * @param string $name
     * @param array $methods
     */
    public function __construct(string $path, array $methods = [], string $name = null)
    {
        $this->name = $name ?? self::generateNameFromPath($path);
        $this->baseRoute = new BaseRoute($path);
        $this->baseRoute->setMethods($methods);
    }

    /**
     * @param string $parameter
     * @param string $regex
     * @return $this
     */
    public function addRule(string $parameter, string $regex): self
    {
        $this->requirements[$parameter] = $regex;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets controller that is going to handle all request
     * which uri matches the route pattern.
     *
     * @param string|callable $handler
     * @return $this
     */
    public function delegate($handler): self
    {
        $this->defaults['-controller'] = $handler;

        return $this;
    }

    /**
     * Returns route pattern.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->baseRoute->getPath();
    }

    /**
     * Returns request methods.
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->baseRoute->getMethods();
    }

    /**
     * Returns controller that listen to request
     * which uri matches the route pattern.
     *
     * @return callable|string|null
     */
    public function getDelegator()
    {
        return $this->defaults['-controller'] ?? null;
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
     * @internal
     * @return BaseRoute
     */
    public function getBaseRoute(): BaseRoute
    {
        $this->baseRoute->setDefaults($this->defaults);
        $this->baseRoute->setRequirements($this->requirements);

        return $this->baseRoute;
    }

    /**
     * Returns attributes extracted from the uri.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->baseRoute->getOptions();
    }

    /**
     * Factories new instance of the route
     * that will be matched against get request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function get(string $path, string $name = null): Route
    {
        return new self($path, [Request::METHOD_GET], $name ?? self::generateNameFromPath($path));
    }

    /**
     * Factories new instance of the route
     * that will be matched against post request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function post(string $path, string $name = null): Route
    {
        return new self($path, [Request::METHOD_POST], $name ?? self::generateNameFromPath($path));
    }

    /**
     * Factories new instance of the route
     * that will be matched against put request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function put(string $path, string $name = null): Route
    {
        return new self($path, [Request::METHOD_PUT], $name ?? self::generateNameFromPath($path));
    }

    /**
     * Factories new instance of the route
     * that will be matched against delete request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function delete(string $path, string $name = null): Route
    {
        return new self($path, [Request::METHOD_DELETE], $name ?? self::generateNameFromPath($path));
    }

    /**
     * Factories new instance of the route
     * that will be matched against patch request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function patch(string $path, string $name = null): Route
    {
        return new self($path, [Request::METHOD_PATCH], $name ?? self::generateNameFromPath($path));
    }

    /**
     * Factories new instance of the route
     * that will be matched against head request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function head(string $path, string $name = null): Route
    {
        return new self($path, [Request::METHOD_HEAD], $name ?? self::generateNameFromPath($path));
    }

    /**
     * Factories new instance of the route
     * that will be matched against options request.
     *
     * @param string $path
     * @param string $name
     * @return Route
     */
    public static function options(string $path, string $name = null): Route
    {
        return new self($path, [Request::METHOD_OPTIONS], $name ?? self::generateNameFromPath($path));
    }

    /**
     * Generates default name from given path expression,
     * /some/{resource} becomes some_resource
     * @param string $path
     * @return string
     */
    private static function generateNameFromPath(string $path): string
    {
        return str_replace(['{', '}', '/'], ['', '', '_'], trim($path, '/'));
    }
}
