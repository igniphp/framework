<?php declare(strict_types=1);

namespace IgniTestFunctional\Http\Server;

use Igni\Http\Server\HttpConfiguration;
use PHPUnit\Framework\TestCase;

final class HttpConfigurationTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $configuration = new HttpConfiguration();
        self::assertInstanceOf(HttpConfiguration::class, $configuration);
        self::assertSame(
            [
                'address' => '0.0.0.0',
                'port' => 80,
            ],
            $configuration->getSettings()
        );
    }

    public function testSettings(): void
    {
        $configuration = new HttpConfiguration();
        self::assertFalse($configuration->isSslEnabled());
        self::assertFalse($configuration->isDaemonEnabled());

        $configuration->enableSsl('a', 'b');
        self::assertTrue($configuration->isSslEnabled());
        $configuration->enableDaemon('pid');
        self::assertTrue($configuration->isDaemonEnabled());
        $configuration->setDispatchMode(HttpConfiguration::DISPATCH_MODULO);
        $configuration->setLogFile('log');
        $configuration->setWorkers(2);
        $configuration->setMaxConnections(20);
        $configuration->setMaximumBacklog(2);
        $configuration->setMaxConnections(10);
        $configuration->setUploadDir('uploadDir');

        self::assertSame(
            [
                'address' => '0.0.0.0',
                'port' => 80,
                'ssl_cert_file' => 'a',
                'ssl_key_file' => 'b',
                'daemonize' => true,
                'pid_file' => 'pid',
                'dispatch_mode' => 2,
                'log_file' => 'log',
                'worker_num' => 2,
                'max_conn' => 10,
                'backlog' => 2,
                'upload_tmp_dir' => 'uploadDir',
            ],
            $configuration->getSettings()
        );
    }
}
