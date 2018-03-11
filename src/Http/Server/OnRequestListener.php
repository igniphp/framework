<?php declare(strict_types=1);

namespace Igni\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface OnRequestListener extends Listener
{
    public function onRequest(ServerRequestInterface $request): ResponseInterface;
}
