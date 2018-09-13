<?php declare(strict_types=1);

namespace Igni\Application\Listeners;

use Igni\Application\Application;

/**
 * Can be implemented by module to perform tasks when application boots-up (modules are loaded but not handled),
 *
 * @package Igni\Application\Listeners
 */
interface OnBootListener
{
    public function onBoot(Application $application): void;
}
