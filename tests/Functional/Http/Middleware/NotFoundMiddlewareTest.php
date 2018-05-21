<?php declare(strict_types=1);

namespace IgniTest\Funcational\Http\Middleware;

use Igni\Http\Exception\NotFoundException;
use Igni\Http\Middleware\NotFoundMiddleware;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFoundMiddlewareTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $middleware = new NotFoundMiddleware();
        self::assertInstanceOf(NotFoundMiddleware::class, $middleware);
    }

    public function testInvokeWithError(): void
    {
        $middleware = new NotFoundMiddleware();
        $requestHandler = Mockery::mock(RequestHandlerInterface::class);
        $requestHandler
            ->shouldReceive('handle')
            ->andThrow(NotFoundException::class);

        $response = $middleware->process(Mockery::mock(ServerRequestInterface::class), $requestHandler);

        self::assertSame(404, $response->getStatusCode());
    }
}
