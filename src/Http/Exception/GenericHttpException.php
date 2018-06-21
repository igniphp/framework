<?php declare(strict_types=1);

namespace Igni\Http\Exception;

use Igni\Exception\RuntimeException;
use Igni\Http\Response;

class GenericHttpException extends RuntimeException implements HttpException
{
    public function getHttpStatusCode(): int
    {
        return $this->getCode();
    }

    public function getHttpBody(): string
    {
        return $this->getMessage();
    }

    public static function invalidUri($uri, $method): self
    {
        return new self("No resource found for $method $uri", Response::HTTP_NOT_FOUND);
    }

    public static function methodNotAllowed(string $uri, string $method, array $allowedMethods): self
    {
        $allowedMethods = implode(', ', $allowedMethods);
        $exception = new self(
            "Method `$method` not allowed. This uri `$uri` allows only $allowedMethods http methods.",
            Response::HTTP_METHOD_NOT_ALLOWED
        );
        return $exception;
    }
}
