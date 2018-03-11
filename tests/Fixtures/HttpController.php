<?php declare(strict_types=1);

namespace IgniTest\Fixtures;

use Igni\Http\Controller;
use Igni\Http\Response;
use Igni\Http\Route;
use Igni\Validation\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpController implements Controller
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return Response::fromText('test controller');
    }

    public static function getRoute(): Route
    {
        Route::get('/testhttpcontroller');
    }

    public function getValidator(): Validator
    {

    }
}
