<?php declare(strict_types=1);

namespace Igni\Http\Exception;

use Exception;
use Igni\Exception\RuntimeException;

class HttpException extends RuntimeException implements HttpExceptionInterface
{
    public function __construct(string $messageBody = "", int $httpStatusCode = 0, Exception $previous = null)
    {
        parent::__construct($messageBody, $httpStatusCode, $previous);
    }

    public function getHttpStatusCode()
    {
        return $this->getCode();
    }

    public function getHttpBody()
    {
        return $this->getMessage();
    }
}
