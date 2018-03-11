<?php
require_once __DIR__.'/../vendor/autoload.php';

/**
 * This example shows how to configure server as a daemon.
 * To kill process simply run from the terminal: kill -9 `cat ./igni.pid`.
 */

use Igni\Http\Application;
use Igni\Http\Response;
use Igni\Http\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$configuration = new Server\HttpConfiguration();

$configuration->setLogFile(__DIR__ . '/igni.log');
$configuration->enableDaemon(__DIR__ . '/igni.pid');

// Setup server
$server = new Server($configuration);

// Setup application and routes
$application = new Application();
$application->get('/hello/{name}', function (ServerRequestInterface $request) : ResponseInterface {
    return Response::fromText("Hello {$request->getAttribute('name')}");
});

// Run the server
$application->run($server);
