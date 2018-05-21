<?php declare(strict_types=1);

namespace IgniTestFunctional\Application;

use Igni\Application\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $config = new Config();
        self::assertInstanceOf(Config::class, $config);
    }
}
