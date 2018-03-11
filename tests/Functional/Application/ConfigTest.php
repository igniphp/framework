<?php declare(strict_types=1);

namespace IgniTestFunctional\Application;

use Igni\Application\Config;
use Igni\Http\Application;
use Igni\Utils\TestCase;

final class ConfigTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $config = new Config();
        self::assertInstanceOf(Config::class, $config);
    }

    public function testFromIni(): void
    {
        $config = Config::fromIni(__DIR__ . '/../../Fixtures/http.ini');

        self::assertSame(Application::class, $config->get('application.class'));
    }
}
