<?php declare(strict_types=1);

namespace Igni\Application\Exception;

use Psr\Http\Message\ResponseInterface;
use Serializable;

class ControllerException extends ApplicationException
{
    public static function forMissingController(string $route): self
    {
        return new self("Could not retrieve controller for route $route");
    }

    public static function forInvalidReturnValue(): self
    {
        return new self(sprintf(
            'Invalid response returned by controller, controller must return %s or %s. Did you forgot return statement?',
            ResponseInterface::class,
            Serializable::class
        ));
    }
}
