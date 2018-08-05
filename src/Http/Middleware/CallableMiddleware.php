<?php declare(strict_types=1);

namespace Igni\Http\Middleware;

use Igni\Http\Exception\MiddlewareException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableMiddleware implements MiddlewareInterface
{
    protected $middleware;

    public function __construct(callable $middleware)
    {
        $this->middleware = $middleware;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = ($this->middleware)($request, $handler);
        if (!$response instanceof ResponseInterface) {
            throw MiddlewareException::forInvalidMiddlewareResponse($response);
        }

        return $response;
    }
}
