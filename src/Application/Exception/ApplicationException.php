<?php declare(strict_types=1);

namespace Igni\Application\Exception;

use Igni\Application\Controller;
use Igni\Exception\RuntimeException;

class ApplicationException extends RuntimeException
{
    public static function forInvalidModule($module): self
    {
        $dumped = var_export($module, true);
        return new self("Passed module (${dumped}) is not valid module.");
    }

    public static function forInvalidController($controller): self
    {
        $dumped = var_export($controller, true);
        return new self(
            "Passed controller (${dumped}) is not valid controller, 
            controller must be callable or implement " . Controller::class . ' interface'
        );
    }
}
