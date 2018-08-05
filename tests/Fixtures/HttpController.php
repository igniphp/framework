<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures;

use Igni\Http\Controller;
use Igni\Http\Response;
use Igni\Http\Route as RouteInterface;
use Igni\Http\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpController implements Controller
{
    public const URI = '/testhttpcontroller';

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return Response::fromText('test controller');
    }

    public static function getRoute(): RouteInterface
    {
        return Route::get(self::URI);
    }
}
