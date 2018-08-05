<?php declare(strict_types=1);

namespace Igni\Http\Exception;

use Igni\Exception\RuntimeException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewareException extends RuntimeException
{
    public static function invalidValueReturnedFromMiddleware(): self
    {
        return new self(sprintf(
            'Middleware has returned invalid value, %s expected. Did you forgot return statement?',
            ResponseInterface::class
        ));
    }

    public static function invalidArgumentsPassedToNextCallback(): self
    {
        return new self(sprintf(
            'Invalid argument(s) passed to `$next` callback, expected %s or/and %s',
            RequestInterface::class,
            ResponseInterface::class
        ));
    }

    public static function forEmptyMiddlewarePipeline(): self
    {
        return new self('Middleware pipeline is empty.');
    }

    public static function forInvalidMiddlewareResponse($response): self
    {
        $dumped = var_export($response, true);
        return new self(sprintf(
            "Middleware failed to produce valid response object, expected instance of `%s` got `%s`".
            ResponseInterface::class,
            $dumped
        ));
    }
}
