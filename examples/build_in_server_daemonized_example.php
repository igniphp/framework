<?php
require_once __DIR__.'/../vendor/autoload.php';

/**
 * This example shows how to configure server as a daemon.
 * To kill process simply run from the terminal: kill -9 `cat ./igni.pid`.
 */

use Igni\Application\HttpApplication;
use Igni\Network\Http\Response;
use Igni\Network\Server\Configuration;
use Igni\Network\Server\HttpServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$configuration = new Configuration(8080, '0.0.0.0');
$configuration->enableDaemon(__DIR__ . '/igni.pid');

// Setup server
$server = new HttpServer($configuration);

// Setup application and routes
$application = new HttpApplication();
$application->get('/hello/{name}', function (ServerRequestInterface $request) : ResponseInterface {
    return Response::asText("Hello {$request->getAttribute('name')}");
});

// Run the server
$application->run($server);
