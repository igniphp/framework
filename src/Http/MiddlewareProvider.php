<?php declare(strict_types=1);

namespace Igni\Http;

interface MiddlewareProvider
{
    public function provideMiddleware(MiddlewareAggregate $aggregate): void;
}
