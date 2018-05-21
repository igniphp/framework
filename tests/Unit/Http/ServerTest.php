<?php declare(strict_types=1);

namespace IgniTestFunctional\Http;

use Igni\Http\Server;
use Mockery;
use PHPUnit\Framework\TestCase;

final class ServerTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(Server::class, new Server());
    }

    public function testAddListener(): void
    {
        // Mock listeners.
        $noopListener = Mockery::mock(Server\Listener::class);
        $onRequestListener = Mockery::mock(Server\OnRequest::class);

        // Mock swoole server.
        $swoole = Mockery::mock(\Swoole\Server::class . '[on]', ['0.0.0.0']);

        $swoole->shouldReceive('on')
            ->with('Request', Mockery::any());

        $server = new Server();
        $server->addListener($noopListener);
        $reflectionApi = new \ReflectionClass(Server::class);
        $listenersProperty = $reflectionApi->getProperty('listeners');
        $listenersProperty->setAccessible(true);
        $handlerProperty = $reflectionApi->getProperty('handler');
        $handlerProperty->setAccessible(true);

        self::assertCount(1, $listenersProperty->getValue($server));

        $handlerProperty->setValue($server, $swoole);
        $server->addListener($onRequestListener);
        self::assertCount(2, $listenersProperty->getValue($server));
    }
}
