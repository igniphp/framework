<?php declare(strict_types=1);

namespace Igni\Application\Listeners;

use Igni\Application\Application;

use Throwable;

/**
 * Can be implemented by module to perform tasks when error occurs, exception can be
 * overridden by the handler - this can be useful when there is requirement for
 * displaying custom responses when given exception occurs.
 *
 * @package Igni\Application\Listeners
 */
interface OnErrorListener
{
    public function onError(Application $application, Throwable $exception): Throwable;
}
