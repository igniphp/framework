<?php declare(strict_types=1);

namespace Igni\Application\Listeners;

use Igni\Application\Application;

interface OnRunListener
{
    public function onRun(Application $application);
}
