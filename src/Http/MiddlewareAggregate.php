<?php declare(strict_types=1);

namespace Igni\Http;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Represents middleware aggregator. Used by http application.
 *
 * @package Igni\Http
 */
interface MiddlewareAggregate
{
    /**
     * @param string|MiddlewareInterface|callable $middleware
     */
    public function use($middleware): void;
}
