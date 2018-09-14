<?php
require_once __DIR__.'/../vendor/autoload.php';

use Igni\Application\HttpApplication;
use Igni\Network\Server\Configuration;
use Igni\Network\Server\HttpServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class Controller
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        throw new \Exception("Dupa");
    }
}

$server = new HttpServer(new Configuration('0.0.0.0', 8080));
$application = new HttpApplication();

$application->get('/hello', new Controller());

$application->run($server);
