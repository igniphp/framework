<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures;

use Igni\Network\Exception\HttpException;
use Igni\Network\Http\Response;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class CustomHttpException extends RuntimeException implements HttpException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function toResponse(): ResponseInterface
    {
        return Response::asJson([
            'error_code' => $this->getCode(),
            'error_message' => $this->getMessage(),
        ], $this->getCode());
    }
}
