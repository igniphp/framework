<?php declare(strict_types=1);

namespace Igni\Http\Server;

class ClientStats
{
    private $stats;

    public function __construct(array $stats)
    {
        $this->stats = $stats;
    }

    public function getPort(): int
    {
        return $this->stats['remote_port'];
    }

    public function getIp(): string
    {
        return $this->stats['remote_ip'];
    }

    public function getConnectTime(): int
    {
        return $this->stats['connect_time'];
    }
}
