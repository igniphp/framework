<?php declare(strict_types=1);

namespace Igni\Application\Listeners;

use Igni\Application\Application;

use Throwable;

interface OnErrorListener
{
    public function onError(Application $application, Throwable $exception);
}
