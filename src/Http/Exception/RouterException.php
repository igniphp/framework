<?php declare(strict_types=1);

namespace Igni\Http\Exception;

use Igni\Exception\RuntimeException;
use Igni\Http\Route;
use Igni\Http\Router;

class RouterException extends RuntimeException implements HttpExceptionInterface
{
    private $httpStatus;

    public static function noRouteMatchesRequestedUri(string $uri): RouterException
    {
        $exception = new self("No route matches requested uri `$uri`.");
        $exception->httpStatus = 404;
        return $exception;
    }

    public static function methodNotAllowed(string $uri, array $allowedMethods): RouterException
    {
        $allowedMethods = implode(', ', $allowedMethods);
        $exception = new self("This uri `$uri` allows only $allowedMethods http methods.");
        $exception->httpStatus = 405;
        return $exception;
    }

    public static function invalidRoute($given): RouterException
    {

        $exception = new self(sprintf(
            '%s::addRoute() - passed value must be instance of %s, %s given.',
            Router::class,
            Route::class,
            get_class($given)
        ));
        $exception->httpStatus = 500;
        return $exception;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatus;
    }

    public function getHttpBody(): string
    {
        return $this->message;
    }
}
