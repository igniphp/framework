<?php declare(strict_types=1);

namespace Igni\Application\Providers;

use Igni\Application\Controller\ControllerAggregate;

interface ControllerProvider
{
    public function provideControllers(ControllerAggregate $controllers): void;
}
