<?php declare(strict_types=1);

namespace Igni\Http;

use Igni\Exception\RuntimeException;
use Igni\Http\Server\Listener;
use Igni\Http\Server\OnRequestListener;
use Igni\Http\Server\HttpConfiguration;
use Swoole;

class Server
{
    private const SWOOLE_EXT_NAME = 'swoole';

    private const DEFAULT_ADDRESS = '0.0.0.0';

    private const DEFAULT_PORT = 8080;

    private $handler;

    public function __construct(HttpConfiguration $configuration = null)
    {
        if (!extension_loaded(self::SWOOLE_EXT_NAME)) {
            throw new RuntimeException('Could not run server without swoole extension.');
        }

        if ($configuration === null) {
            $this->handler = new Swoole\Http\Server(self::DEFAULT_ADDRESS, self::DEFAULT_PORT);
        } else {
            if ($configuration->isSslEnabled()) {
                $flags = SWOOLE_SOCK_TCP | SWOOLE_SSL;
            } else {
                $flags = SWOOLE_SOCK_TCP;
            }
            $settings = $configuration->getSettings();

            $this->handler = new Swoole\Http\Server($settings['address'], $settings['port'], SWOOLE_PROCESS, $flags);
            $this->handler->set($settings);
        }
    }

    public function addListener(Listener $listener): void
    {
        if ($listener instanceof OnRequestListener) {
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

    }

    public function run(): void
    {
        $this->handler->start();
    }

    public function stop(): void
    {
        $this->handler->shutdown();
    }
}
