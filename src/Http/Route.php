<?php declare(strict_types=1);

namespace Igni\Http;

/**
 * Represents expression that is matched against requested uri.
 * Route should be immutable object.
 *
 * @package Igni\Http
 */
interface Route
{
    /**
     * Return instance with specific attributes
     * @param array $attributes
     * @return Route
     */
    public function withAttributes(array $attributes): Route;

    /**
     * Returns route's attributes extracted from requested uri
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Returns instance with specific controller
     * @param $controller callable or Controller instance
     * @return Route
     */
    public function withController($controller): Route;

    /**
     * Should return callable that accepts psr-request and return psr-response instances or Controller instance.
     * @see Controller
     * @return callable
     */
    public function getController();

    /**
     * Returns expression that is later matched against requested uri.
     * @return string
     */
    public function getPath(): string;

    /**
     * Returns route that can be matched against specified methods.
     * @param array $methods
     * @return Route
     */
    public function withMethods(array $methods): Route;

    /**
     * Returns requested methods that are accepted by the route.
     * @return array
     */
    public function getMethods(): array;
}
