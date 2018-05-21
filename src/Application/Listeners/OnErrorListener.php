<?php declare(strict_types=1);

namespace Igni\Application\Listeners;

use Igni\Application\Application;

use Throwable;

/**
 * Can be implemented by module to perform tasks when error occurs
 * when application is running.
 *
 * @package Igni\Application\Listeners
 */
interface OnErrorListener
{
    /**
     * Keeps on-error listener logic.
     *
     * @param Application $application
     * @param Throwable $exception
     * @return mixed
     */
    public function onError(Application $application, Throwable $exception);
}
