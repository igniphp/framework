<?php declare(strict_types=1);

namespace Igni\Application\Providers;

use Igni\Application\Http\MiddlewareAggregator;

/**
 * Allows modules to provide additional psr-15 compatible middleware.
 *
 * @package Igni\Http
 */
interface MiddlewareProvider
{
    public function provideMiddleware(MiddlewareAggregator $aggregate): void;
}
