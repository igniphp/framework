<?php declare(strict_types=1);

namespace Igni\Http\Middleware;

use Igni\Http\Exception\NotFoundException;
use Igni\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware for generating default response in case application cannot find route definition for client's request.
 *
 * @package Igni\Http\Middleware
 */
final class NotFoundMiddleware implements MiddlewareInterface
{
    /**
     * @see MiddlewareInterface::process()
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
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
