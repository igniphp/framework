<?php declare(strict_types=1);

namespace Igni\Http;

/**
 * Responsible for aggregating routes and forwarding request between framework and application layer.
 *
 * @package Igni\Http
 */
interface Router
{
    public function addRoute(Route $route): void;
    public function findRoute(string $method, string $path): Route;
}
