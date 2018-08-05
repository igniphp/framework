<?php declare(strict_types=1);

namespace Igni\Http\Exception;

use Igni\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;

class HttpModuleException extends RuntimeException
{
    public static function couldNotRetrieveControllerForRoute($route): self
    {
        return new self("Could not retrieve controller for route $route");
    }

    public static function controllerMustReturnValidResponse(): self
    {
        return new self(sprintf(
            'Invalid response returned by controller, controller must return %s or %s. Did you forgot return statement?',
            ResponseInterface::class,
            \Serializable::class
        ));
    }
}
