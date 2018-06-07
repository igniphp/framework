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
 *
 * @package Igni\Http
 */
class Server
{
    private const SWOOLE_EXT_NAME = 'swoole';

    /**
     * @var Swoole\Server|null
     */
    private $handler;

    /**
     * @var HttpConfiguration
     */
    private $settings;

    /**
     * @var Listener[]
     */
    private $listeners = [];

    public function __construct(HttpConfiguration $settings = null)
    {
        if (!extension_loaded(self::SWOOLE_EXT_NAME)) {
            throw new RuntimeException('Could not run server without swoole extension.');
        }

        if ($settings === null) {
            $settings = new HttpConfiguration();
        }

        $this->settings = $settings;
    }

    /**
     * Adds listener that is attached to server once it is run.
     *
     * @param Listener $listener
     */
    public function addListener(Listener $listener): void
    {
        $this->listeners[] = $listener;
        if ($this->handler !== null) {
            $this->attachListener($listener);
        }
    }

    /**
     * Checks if listener exists.
     *
     * @param Listener $listener
     * @return bool
     */
    public function hasListener(Listener $listener): bool
    {
        return in_array($listener, $this->listeners);
    }

    /**
     * Returns information about client.
     *
     * @param int $clientId
     * @return ClientStats
     */
    public function getClientStats(int $clientId): ClientStats
    {
        return new ClientStats($this->handler->getClientInfo($clientId));
    }

    /**
     * Returns information about server.
     *
     * @return ServerStats
     */
    public function getServerStats(): ServerStats
    {
        return new ServerStats($this->handler->stats());
    }

    /**
     * Starts the server.
     */
    public function start(): void
    {
        $flags = SWOOLE_SOCK_TCP;
        if ($this->settings->isSslEnabled()) {
            $flags |= SWOOLE_SSL;
        }

        $settings = $this->settings->getSettings();

        if (!defined('IS_TEST') || IS_TEST !== true) {
            $this->handler = new Swoole\Http\Server($settings['address'], $settings['port'], SWOOLE_PROCESS, $flags);
        }
        $this->handler->set($settings);

        // Attach listeners.
        foreach ($this->listeners as $listener) {
            $this->attachListener($listener);
        }

        // Start the server.
        $this->handler->start();
    }

    /**
     * Stops the server.
     */
    public function stop(): void
    {
        if ($this->handler !== null) {
            $this->handler->shutdown();
            $this->handler = null;
        }
    }

    private function attachListener(Listener $listener): void
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

            // Protect server software header.
            $response->header('software-server', '');

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
