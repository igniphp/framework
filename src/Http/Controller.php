<?php declare(strict_types=1);

namespace Igni\Http;

use Igni\Application\Controller as ControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @method getValidator(): Validator
 */
interface Controller extends ControllerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|\Serializable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface;

    /**
     * @return \Igni\Http\Route
     */
    public static function getRoute(): Route;
}
