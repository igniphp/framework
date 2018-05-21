<?php declare(strict_types=1);

namespace Igni\Application\Providers;

use Igni\Application\Config;

/**
 * Can be implemented by module to provide additional configuration
 * for application.
 *
 * @package Igni\Application\Providers
 */
interface ConfigProvider
{
    /**
     * @param Config $config
     */
    public function provideConfig(Config $config): void;
}
