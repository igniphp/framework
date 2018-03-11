<?php declare(strict_types=1);

namespace Igni\Http;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareAggregate
{
    /**
     * @param string|MiddlewareInterface $middleware
     */
    public function use($middleware): void;
}
