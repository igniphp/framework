<?php declare(strict_types=1);

namespace Igni\Http\Server;

use Igni\Http\Server;

/**
 * This event happens when the new connection comes in.
 */
interface OnConnect extends Listener
{
    /**
     * Handles connect server event.
     *
     * @param Server $server
     * @param int $clientId
     */
    public function onConnect(Server $server, int $clientId): void;
}
