<?php declare(strict_types=1);

namespace Igni\Http\Server;

class ServerStats
{
    private $stats;

    public function __construct(array $stats)
    {
        $this->stats = $stats;
    }

    /**
     * Returns the timestamp of server since start
     *
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->stats['start_time'];
    }

    /**
     * Returns the number of current connections
     *
     * @return int
     */
    public function getConnections(): int
    {
        return $this->stats['connection_num'];
    }

    /**
     * Returns the number of accepted connections
     *
     * @return int
     */
    public function getAcceptedConnections(): int
    {
        return $this->stats['accept_count'];
    }

    /**
     * Returns the number of closed connections
     *
     * @return int
     */
    public function getClosedConnections(): int
    {
        return $this->stats['close_count'];
    }

    /**
     * Returns the number of queuing up tasks
     *
     * @return int
     */
    public function getAwaitingConnections(): int
    {
        return $this->stats['close_count'];
    }

    /**
     * Returns the number of received requests
     *
     * @return int
     */
    public function getReceivedRequests(): int
    {
        return $this->stats['request_count'];
    }
}
