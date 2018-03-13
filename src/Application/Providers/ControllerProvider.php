<?php declare(strict_types=1);

namespace Igni\Application\Providers;

use Igni\Application\Controller\ControllerAggregate;

/**
 * Can be implemented by module to register controllers in the application scope.
 *
 * @package Igni\Application\Providers
 */
interface ControllerProvider
{
    /**
     * @param ControllerAggregate $controllers
     */
    public function provideControllers(ControllerAggregate $controllers): void;
}
