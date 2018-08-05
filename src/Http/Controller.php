<?php declare(strict_types=1);

namespace Igni\Http;

use Igni\Application\Controller as ControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * PSR-7 and SRP compatible interface for http controller.
 * Each controller must handle only one type of request defined by route.
 *
 * @package Igni\Http
 */
interface Controller extends ControllerInterface
{
    /**
     * Controller should accept server request and return valid response interface.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface|\Serializable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface;

    /**
     * Returns the route pattern that will be handled by controller.
     *
     * @return \Igni\Http\Route
     */
    public static function getRoute(): Route;
}
