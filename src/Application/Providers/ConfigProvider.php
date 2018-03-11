<?php declare(strict_types=1);

namespace Igni\Application\Providers;

use Igni\Application\ConfigInterface;

interface ConfigProvider
{
    public function provideConfig(ConfigInterface $config): void;
}
