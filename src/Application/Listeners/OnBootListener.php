<?php declare(strict_types=1);

namespace Igni\Application\Listeners;

use Igni\Application\Application;

interface OnBootListener
{
    public function onBoot(Application $application);
}
