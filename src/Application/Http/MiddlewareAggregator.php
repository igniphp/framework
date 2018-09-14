<?php declare(strict_types=1);

namespace Igni\Application\Http;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Used by http application.
 *
 * @package Igni\Application
 */
interface MiddlewareAggregator
{
    /**
     * @param string|MiddlewareInterface|callable $middleware
     */
    public function use($middleware): void;
}
