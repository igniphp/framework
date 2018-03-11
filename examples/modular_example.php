<?php
require_once __DIR__.'/../vendor/autoload.php';

use Igni\Application\Controller\ControllerAggregate;
use Igni\Application\Providers\ControllerProvider;
use Igni\Http\Application;
use Igni\Http\Response;
use Igni\Http\Route;
use Igni\Http\Server;

/**
 * Module definition.
 */
class SimpleModule implements ControllerProvider
{
    /**
     * @param \Igni\Http\Controller\ControllerAggregate $controllers
     */
    public function provideControllers(ControllerAggregate $controllers): void
    {
        $controllers->add(function ($request) {
            return Response::fromText("Hello {$request->getAttribute('name')}!");
        }, Route::get('/hello/{name}'));
    }
}

$application = new Application();

// Extend application with the module.
$application->extend(\SimpleModule::class);

// Run the application.
$application->run(new Server());
