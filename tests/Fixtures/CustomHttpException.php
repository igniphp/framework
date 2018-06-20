<?php declare(strict_types=1);

namespace IgniTest\Fixtures;

use Igni\Http\Exception\HttpException;
use RuntimeException;
use Throwable;

class CustomHttpException extends RuntimeException implements HttpException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getHttpStatusCode()
    {
        return $this->getCode();
    }

    public function getHttpBody()
    {
        return json_encode([
            'error_code' => $this->getCode(),
            'error_message' => $this->getMessage(),
        ]);
    }
}
