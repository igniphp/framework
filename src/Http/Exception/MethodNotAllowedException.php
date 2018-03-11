<?php declare(strict_types=1);

namespace Igni\Http\Exception;

class MethodNotAllowedException extends HttpException
{
    public static function methodNotAllowed($uri, $method, $allowedMethods)
    {
        $allowedMethods = implode(', ', $allowedMethods);
        $exception = new self(
            "Method `$method` not allowed. This uri `$uri` allows only $allowedMethods http methods.",
            405
        );
        return $exception;
    }
}
