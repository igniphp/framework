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
    /**
     * Keeps on-shutdown listener logic.
     *
     * @param Application $application
     * @return mixed
     */
    public function onShutDown(Application $application);
}
