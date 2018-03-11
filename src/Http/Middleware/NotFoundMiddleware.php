<?php declare(strict_types=1);

namespace Igni\Http\Middleware;

use Igni\Http\Exception\NotFoundException;
use Igni\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NotFoundMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        try {
            $response = $next->handle($request);
        } catch (NotFoundException $exception) {
            $response = Response::fromText('Not Found', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }
}
