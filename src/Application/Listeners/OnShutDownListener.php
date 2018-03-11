<?php declare(strict_types=1);

namespace Igni\Application\Listeners;

use Igni\Application\Application;

interface OnShutDownListener
{
    public function onShutDown(Application $application);
}
