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
    public function onRun(Application $application): void;
}
