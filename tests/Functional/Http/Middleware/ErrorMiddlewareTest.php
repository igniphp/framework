<?php declare(strict_types=1);

namespace IgniTest\Funcational\Http\Middleware;

use Igni\Application\Exception\ApplicationException;
use Igni\Http\Middleware\ErrorMiddleware;
use IgniTest\Fixtures\CustomHttpException;
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

    public function testWithCustomException(): void
    {
        $error = new CustomHttpException('Nothing to see here', 400);
        $middleware = new ErrorMiddleware(function() {});
        $requestHandler = new class($error) implements RequestHandlerInterface {
            private $error;

            public function __construct(CustomHttpException $error)
            {
                $this->error = $error;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw $this->error;
            }
        };

        $response = $middleware->process(Mockery::mock(ServerRequestInterface::class), $requestHandler);

        self::assertSame($error->getHttpStatusCode(), $response->getStatusCode());
        self::assertSame($error->getHttpBody(), (string) $response->getBody());
    }
}
