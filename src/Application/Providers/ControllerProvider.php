<?php declare(strict_types=1);

namespace Igni\Application\Providers;

use Igni\Application\ControllerAggregator;

/**
 * Can be implemented by module to register controllers in the application scope.
 *
 * @package Igni\Application\Providers
 */
interface ControllerProvider
{
    public function provideControllers(ControllerAggregator $aggregator): void;
}
