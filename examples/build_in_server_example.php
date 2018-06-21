<?php
require_once __DIR__.'/../vendor/autoload.php';

use Igni\Http\Application;
use Igni\Http\Response;
use Igni\Http\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// Setup server
$server = new Server(new Server\HttpConfiguration('0.0.0.0', 8080));

// Setup application and routes
$application = new Application();
$application->get('/hello/{name}', function (ServerRequestInterface $request) : ResponseInterface {
    return Response::fromText("Hello {$request->getAttribute('name')}");
});

// Run the server, it should listen on localhost:8080
$application->run($server);
