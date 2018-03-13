<?php declare(strict_types=1);

namespace Igni\Http\Server;

/**
 * Value class for aggregating client statistics.
 *
 * @package Igni\Http\Server
 */
class ClientStats
{
    private $stats;

    /**
     * ClientStats constructor.
     *
     * @param array $stats
     */
    public function __construct(array $stats)
    {
        $this->stats = $stats;
    }

    /**
     * Returns port used by client to connect to the server.
     *
     * @return int
     */
    public function getPort(): int
    {
        return $this->stats['remote_port'];
    }

    /**
     * Returns client's ip.
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->stats['remote_ip'];
    }

    /**
     * Returns unix timestamp when connection happened.
     *
     * @return int
     */
    public function getConnectTime(): int
    {
        return $this->stats['connect_time'];
    }
}
