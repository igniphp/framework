<?php declare(strict_types=1);

namespace Igni\Application\Listeners;

use Igni\Application\Application;

/**
 * Can be implemented by module to perform clean-up tasks.
 *
 * @package Igni\Application\Listeners
 */
interface OnShutDownListener
{
    public function onShutDown(Application $application): void;
}
