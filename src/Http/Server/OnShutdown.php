<?php declare(strict_types=1);

namespace Igni\Http\Server;

use Igni\Http\Server;

/**
 * The event happens when the server shuts down
 *
 * Before the shutdown happens all the client connections are closed.
 */
interface OnShutdown extends Listener
{
    public function onShutdown(Server $server): void;
}
