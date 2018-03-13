<?php declare(strict_types=1);

namespace Igni\Application\Listeners;

use Igni\Application\Application;

/**
 * Can be implemented by module to perform tasks when application has already loaded modules and
 * configuration is loaded.
 *
 * @package Igni\Application\Listeners
 */
interface OnRunListener
{
    /**
     * Keeps on-run listener logic.
     *
     * @param Application $application
     * @return mixed
     */
    public function onRun(Application $application);
}
