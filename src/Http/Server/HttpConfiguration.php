<?php declare(strict_types=1);

namespace Igni\Http\Server;

/**
 * Class HttpConfiguration
 * @package Igni\Http\Server
 */
class HttpConfiguration
{
    public const DISPATCH_ROUND_ROBIN = 1;
    public const DISPATCH_MODULO = 2;
    public const DISPATCH_PREEMPTIVE_ASSIGNMENT = 3;
    public const DEFAULT_ADDRESS = '0.0.0.0';
    public const DEFAULT_PORT = 8080;

    /**
     * @var array
     */
    private $settings = [];

    /**
     * HttpConfiguration constructor.
     *
     * @param string $address
     * @param int $port
     */
    public function __construct(string $address = self::DEFAULT_ADDRESS, int $port = self::DEFAULT_PORT)
    {
        $this->settings['address'] = $address;
        $this->settings['port'] = $port;
    }

    /**
     * Checks if ssl is enabled.
     *
     * @return bool
     */
    public function isSslEnabled(): bool
    {
        return isset($this->settings['ssl_cert_file']);
    }

    /**
     * Enables ssl on the server
     *
     * @param string $certFile
     * @param string $keyFile
     */
    public function enableSsl(string $certFile, string $keyFile): void
    {
        $this->settings += [
            'ssl_cert_file' => $certFile,
            'ssl_key_file' => $keyFile,
        ];
    }

    /**
     * Checks if server is daemonized.
     *
     * @return bool
     */
    public function isDaemonEnabled(): bool
    {
        return isset($this->settings['daemonize']) && $this->settings['daemonize'];
    }

    /**
     * Sets the max tcp connection number of the server.
     *
     * @param int $max
     */
    public function setMaxConnections(int $max = 10000): void
    {
        $this->settings['max_conn'] = $max;
    }

    /**
     * Sets the number of worker processes.
     *
     * @param int $count
     */
    public function setWorkers(int $count = 1): void
    {
        $this->settings['worker_num'] = $count;
    }

    /**
     * Sets the number of requests processed by the worker process before been recycled.
     *
     * @param int $max
     */
    public function setMaxRequests(int $max = 0): void
    {
        $this->settings['max_request'] = $max;
    }

    /**
     * Sets path to the file that will be used to persist server log.
     *
     * @param string $filename
     */
    public function setLogFile(string $filename): void
    {
        $this->settings['log_file'] = $filename;
    }

    /**
     * Sets the maximum number of pending connections. This refers to the number of clients
     * that can be waiting to be served. Exceeding this number results in the client getting
     * an error when attempting to connect.
     *
     * @param int $max
     */
    public function setMaximumBacklog(int $max = 0): void
    {
        $this->settings['backlog'] = $max;
    }

    /**
     * Sets dispatch mode for child processes.
     *
     * @param int $mode
     */
    public function setDispatchMode(int $mode = self::DISPATCH_ROUND_ROBIN): void
    {
        $this->settings['dispatch_mode'] = $mode;
    }

    /**
     * Allows server to be run as a background process.
     *
     * @param string $pid
     */
    public function enableDaemon(string $pid): void
    {
        $this->settings += [
            'daemonize' => true,
            'pid_file' => $pid,
        ];
    }

    /**
     * Sets temporary dir for uploaded files
     *
     * @param string $dir
     */
    public function setUploadDir(string $dir): void
    {
        $this->settings['upload_tmp_dir'] = $dir;
    }

    /**
     * Returns swoole compatible settings array.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
}
