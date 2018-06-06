<?php declare(strict_types=1);

namespace IgniTest\Fixtures;

use Igni\Http\Controller;
use Igni\Http\Response;
use Igni\Http\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpController implements Controller
{
    public const URI = '/testhttpcontroller';

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return Response::fromText('test controller');
    }

    public static function getRoute(): Route
    {
        return Route::get(self::URI);
    }
}
