<?php declare(strict_types=1);

namespace IgniTest\Funcational\Http\Middleware;

use Igni\Http\Middleware\NotFoundMiddleware;
use Igni\Utils\TestCase;

class NotFoundMiddlewareTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $middleware = new NotFoundMiddleware();
        self::assertInstanceOf(NotFoundMiddleware::class, $middleware);
    }

    public function testInvokeWithError(): void
    {

    }
}
