<?php declare(strict_types=1);

namespace Igni\Application\Providers;

use Psr\Container\ContainerInterface;

interface ServiceProvider
{
    public function provideServices(ContainerInterface $container): void;
}
