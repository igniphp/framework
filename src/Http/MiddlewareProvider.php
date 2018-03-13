<?php declare(strict_types=1);

namespace Igni\Http;

/**
 * Allows modules to provide additional psr-15 compatible middleware.
 *
 * @package Igni\Http
 */
interface MiddlewareProvider
{
    /**
     * Registers new middleware in the application scope.
     *
     * @param MiddlewareAggregate $aggregate
     */
    public function provideMiddleware(MiddlewareAggregate $aggregate): void;
}
