<?php declare(strict_types=1);

namespace Igni\Application\Http;

use Igni\Application\Controller as IgniApplicationController;
use Igni\Network\Http\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Represents generic application's controller.
 *
 * Because php does not support generic types, empty interface should be good enough
 * to provide consistency in the application flow for controller handling.
 *
 * @package Igni\Application
 */
interface Controller extends IgniApplicationController
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface;

    public static function getRoute(): Route;
}
