<?php declare(strict_types=1);

namespace IgniTest\Funcational\Http\Middleware;

use Igni\Application\Exception\ApplicationException;
use Igni\Http\Middleware\ErrorMiddleware;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorMiddlewareTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $middleware = new ErrorMiddleware(function() {});
        self::assertInstanceOf(ErrorMiddleware::class, $middleware);
    }

    public function testInvokeWithException(): void
    {
        $middleware = new ErrorMiddleware(function() {});
        $requestHandler = Mockery::mock(RequestHandlerInterface::class);
        $requestHandler
            ->shouldReceive('handle')
            ->andThrow(ApplicationException::class);

        $response = $middleware->process(Mockery::mock(ServerRequestInterface::class), $requestHandler);

        self::assertSame(500, $response->getStatusCode());
    }

    public function testInvokeWithError(): void
    {
        $middleware = new ErrorMiddleware(function() {});
        $requestHandler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $a = $call['undefined'];
            }
        };

        $response = $middleware->process(Mockery::mock(ServerRequestInterface::class), $requestHandler);

        self::assertSame(500, $response->getStatusCode());
    }
}
