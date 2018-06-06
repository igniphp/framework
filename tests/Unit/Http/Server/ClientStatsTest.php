<?php declare(strict_types=1);

namespace IgniTestFunctional\Http\Server;

use Igni\Http\Server\ClientStats;
use PHPUnit\Framework\TestCase;

final class ClientStatsTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(ClientStats::class, new ClientStats([]));
    }

    public function testGetPort(): void
    {
        $stats = new ClientStats([
            'remote_port' => 80
        ]);

        self::assertSame(80, $stats->getPort());
    }

    public function testGetConnectionTime(): void
    {
        $stats = new ClientStats([
            'connect_time' => 180
        ]);

        self::assertSame(180, $stats->getConnectTime());
    }

    public function testGetIp(): void
    {
        $stats = new ClientStats([
            'remote_ip' => '0.0.0.0'
        ]);

        self::assertSame('0.0.0.0', $stats->getIp());
    }
}
