<?php declare(strict_types=1);

namespace IgniTestFunctional\Http\Server;

use Igni\Http\Server\ServerStats;
use PHPUnit\Framework\TestCase;

final class ServerStatsTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(ServerStats::class, new ServerStats([]));
    }

    public function testGetStats(): void
    {
        $stats = new ServerStats([
            'start_time' => 1409831644,  // the time of server since start
            'connection_num' => 1,       // the number of current connections
            'accept_count' => 1,         // the number of connections accepted
            'close_count' => 0,          // the number of connections closed
            'tasking_num' => 1,          // the number of task which is queuing up
            'request_count' => 11,       // the number of request received
            'worker_request_count' => 2,      // the number of request received by the current worker
            'task_queue_num' => 10,      // the number of task which is in queue of task
            'task_queue_bytes' => 65536,
        ]);

        self::assertSame(1, $stats->getAcceptedConnections());
        self::assertSame(0, $stats->getClosedConnections());
        self::assertSame(1, $stats->getConnections());
        self::assertSame(11, $stats->getReceivedRequests());
        self::assertSame(1409831644, $stats->getStartTime());
    }
}
