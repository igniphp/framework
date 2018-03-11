<?php declare(strict_types=1);

namespace Igni\Application\Providers;

use Igni\Application\Config;

interface ConfigProvider
{
    public function provideConfig(Config $config): void;
}
