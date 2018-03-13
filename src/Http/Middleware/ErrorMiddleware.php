<?php declare(strict_types=1);

namespace Igni\Http\Middleware;

use Igni\Http\Exception\HttpException;
use Igni\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use ErrorException;

/**
 * Middleware for error handling. If an exception is thrown and not catch during the request cycle,
 * it will appear here. Middleware will catch it and return response with status code (500) and exception message
 * as a body.
 *
 * @package Igni\Http\Middleware
 */
final class ErrorMiddleware implements MiddlewareInterface
{
    private $errorHandler;

    /**
     * ErrorMiddleware constructor.
     *
     * @param callable $errorHandler
     */
    public function __construct(callable $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @see MiddlewareInterface::process
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $this->setErrorHandler();

        try {
            $response = $next->handle($request);

        } catch (Throwable $exception) {
            ($this->errorHandler)($exception);

            if ($exception instanceof HttpException) {
                $response = Response::fromText($exception->getHttpBody(), $exception->getHttpStatusCode());
            } else {
                $response = Response::fromText((string) $exception, Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        }

        $this->restoreErrorHandler();

        return $response;
    }


    private function setErrorHandler(): void
    {
        set_error_handler(function (int $number, string $message, string $file, int $line) {

            if (!(error_reporting() & $number)) {
                return;
            }

            throw new ErrorException($message, 0, $number, $file, $line);
        });
    }

    private function restoreErrorHandler(): void
    {
        restore_error_handler();
    }
}
