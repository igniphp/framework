<?php
namespace Examples\Controllers;

use Igni\Http\Controller;
use Igni\Http\Response;
use Igni\Http\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GoodbyeController implements Controller
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return Response::fromText('Goodbye cruel world!');
    }

    public static function getRoute(): Route
    {
        return Route::get('/goodbye');
    }
}
