<?php declare(strict_types=1);

namespace Igni\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The event happens when the worker process receives the request data.
 */
interface OnRequest extends Listener
{
    /**
     * Handles client request.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function onRequest(ServerRequestInterface $request): ResponseInterface;
}
