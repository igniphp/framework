<?php declare(strict_types=1);

namespace Igni\Application\Exception;

use Igni\Exception\RuntimeException;
use Psr\Container\NotFoundExceptionInterface;

class ControllerException extends RuntimeException implements NotFoundExceptionInterface
{
    public static function forUndefinedController(string $controller): ControllerException
    {
        return new self("Controller ${controller} not found.");
    }

    public static function forMissingOption(string $option): ControllerException
    {
        return new self("Missing option `${option}` not found.");
    }
}
