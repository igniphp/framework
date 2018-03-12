<?php declare(strict_types=1);

namespace Igni\Http\Server;

use Igni\Http\Server;

/**
 * The event happens when the TCP connection between the client and the server is closed.
 */
interface OnClose extends Listener
{
    public function onClose(Server $server, int $clientId): void;
}
