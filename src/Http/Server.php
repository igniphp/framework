<?php declare(strict_types=1);

namespace Igni\Http;

use Igni\Exception\RuntimeException;
use Igni\Http\Server\ClientStats;
use Igni\Http\Server\OnConnect;
use Igni\Http\Server\OnShutdown;
use Igni\Http\Server\OnStart;
use Igni\Http\Server\ServerStats;
use Igni\Http\Server\HttpConfiguration;
use Igni\Http\Server\Listener;
use Igni\Http\Server\OnClose;
use Igni\Http\Server\OnRequest;
use Swoole;

/**
 * Http server implementation based on swoole extension.
 */
class Server
{
    private const SWOOLE_EXT_NAME = 'swoole';

    private $handler;

    public function __construct(HttpConfiguration $configuration = null)
    {
        if (!extension_loaded(self::SWOOLE_EXT_NAME)) {
            throw new RuntimeException('Could not run server without swoole extension.');
        }

        if ($configuration === null) {
            $configuration = new HttpConfiguration();
        }

        if ($configuration->isSslEnabled()) {
            $flags = SWOOLE_SOCK_TCP | SWOOLE_SSL;
        } else {
            $flags = SWOOLE_SOCK_TCP;
        }

        $settings = $configuration->getSettings();
        $this->handler = new Swoole\Http\Server($settings['address'], $settings['port'], SWOOLE_PROCESS, $flags);
        $this->handler->set($settings);
    }

    public function addListener(Listener $listener): void
    {
        if ($listener instanceof OnRequest) {
            $this->attachOnRequestListener($listener);
        }

        if ($listener instanceof OnConnect) {
            $this->attachOnConnectListener($listener);
        }

        if ($listener instanceof OnClose) {
            $this->attachOnCloseListener($listener);
        }

        if ($listener instanceof OnShutdown) {
            $this->attachOnShutdownListener($listener);
        }

        if ($listener instanceof OnStart) {
            $this->attachOnStartListener($listener);
        }
    }

    public function getClientStats(int $clientId): ClientStats
    {
        return new ClientStats($this->handler->getClientInfo($clientId));
    }

    public function getServerStats(): ServerStats
    {
        return new ServerStats($this->handler->stats());
    }

    public function run(): void
    {
        $this->handler->start();
    }

    public function stop(): void
    {
        $this->handler->shutdown();
    }

    private function attachOnRequestListener(OnRequest $listener): void
    {
        $this->handler->on('Request', function(Swoole\Http\Request $request, Swoole\Http\Response $response) use ($listener) {
            $psrRequest = ServerRequest::fromSwooleRequest($request);
            $psrResponse = $listener->onRequest($psrRequest);

            // Set headers
            foreach ($psrResponse->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    $response->header($name, $value);
                }
            }

            // Status code
            $response->status($psrResponse->getStatusCode());

            // End.
            $response->end($psrResponse->getBody()->getContents());
        });
    }

    private function attachOnConnectListener(OnConnect $listener): void
    {
        $this->handler->on('Connect', function($handler, $clientId) use ($listener) {
            $listener->onConnect($this, $clientId);
        });
    }

    private function attachOnCloseListener(OnClose $listener): void
    {
        $this->handler->on('Close', function($handler, $clientId) use ($listener) {
            $listener->onClose($this, $clientId);
        });
    }

    private function attachOnShutdownListener(OnShutdown $listener): void
    {
        $this->handler->on('Shutdown', function() use ($listener) {
            $listener->onShutdown($this);
        });
    }

    private function attachOnStartListener(OnStart $listener): void
    {
        $this->handler->on('Start', function() use ($listener) {
            $listener->onStart($this);
        });
    }
}
