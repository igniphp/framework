<?php declare(strict_types=1);

namespace Igni\Http\Exception;

use Igni\Exception\RuntimeException;
use Igni\Http\MiddlewareComposer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewareException extends RuntimeException
{
    public static function invalidValueReturnedFromMiddleware()
    {
        return new self(sprintf(
            'Middleware has returned invalid value, %s expected. Did you forgot return statement?',
            ResponseInterface::class
        ));
    }

    public static function invalidArgumentsPassedToNextCallback()
    {
        return new self(sprintf(
            'Invalid argument(s) passed to `$next` callback, expected %s or/and %s',
            RequestInterface::class,
            ResponseInterface::class
        ));
    }
}
