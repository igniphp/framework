<?php declare(strict_types=1);

namespace Igni\Application\Providers;

use Igni\Container\ServiceLocator;
use Psr\Container\ContainerInterface;

/**
 * Can be implemented by module to register additional services.
 *
 * @package Igni\Application\Providers
 */
interface ServiceProvider
{
    /**
     * @param ServiceLocator|ContainerInterface $container
     */
    public function provideServices(ContainerInterface $container): void;
}
