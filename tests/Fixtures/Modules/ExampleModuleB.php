<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures\Modules;

use Igni\Application\Providers\ServiceProvider;
use Igni\Container\ServiceLocator;
use Psr\Container\ContainerInterface;

class ExampleModuleB implements ServiceProvider
{
    /**
     * @param ServiceLocator $container
     */
    public function provideServices(ContainerInterface $container): void
    {

    }
}
