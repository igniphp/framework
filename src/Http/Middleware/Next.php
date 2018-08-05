<?php declare(strict_types=1);

namespace Igni\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

/**
 * Iterates a queue of middleware and executes them.
 */
final class Next implements RequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $parent;

    /**
     * @var SplQueue
     */
    private $queue;

    /**
     * Next constructor.
     * @param SplQueue $queue
     * @param RequestHandlerInterface $parent
     */
    public function __construct(SplQueue $queue, RequestHandlerInterface $parent)
    {
        $this->queue = clone $queue;
        $this->parent = $parent;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return $this->parent->handle($request);
        }

        $middleware = $this->queue->dequeue();

        return $middleware->process($request, $this);
    }
}
