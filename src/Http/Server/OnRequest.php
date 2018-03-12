<?php declare(strict_types=1);

namespace Igni\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The event happens when the worker process receives the request data.
 */
interface OnRequest extends Listener
{
    public function onRequest(ServerRequestInterface $request): ResponseInterface;
}
