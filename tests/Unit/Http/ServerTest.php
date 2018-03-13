<?php declare(strict_types=1);

namespace IgniTestFunctional\Http;

use Igni\Http\Server;
use Igni\Utils\TestCase;
use Mockery;

final class ServerTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(Server::class, new Server());
    }

    public function testAddListener(): void
    {
        // Mock listeners.
        $noopListener = self::mock(Server\Listener::class);
        $onRequestListener = self::mock(Server\OnRequest::class);

        // Mock swoole server.
        $swoole = self::mock(\Swoole\Server::class . '[on]', ['0.0.0.0']);

        $swoole->shouldReceive('on')
            ->with('Request', Mockery::any());

        $server = new Server();
        $server->addListener($noopListener);

        self::assertCount(1, self::readProperty($server, 'listeners'));

        self::writeProperty($server, 'handler', $swoole);
        $server->addListener($onRequestListener);
        self::assertCount(2, self::readProperty($server, 'listeners'));
    }
}
