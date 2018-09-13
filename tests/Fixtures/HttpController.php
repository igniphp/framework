<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures;

use Igni\Application\Http\Controller;
use Igni\Network\Http\Route;
use Igni\Network\Http\Response;
use Igni\Network\Http\Route as IgniNetworkHttpRoute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpController implements Controller
{
    public const URI = '/testhttpcontroller';

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return Response::asText('test controller');
    }

    public static function getRoute(): IgniNetworkHttpRoute
    {
        return Route::get(self::URI);
    }
}
