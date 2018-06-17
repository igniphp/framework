<?php
namespace Examples\Modules;

use Examples\Controllers\GoodbyeController;
use Igni\Application\Controller\ControllerAggregate;
use Igni\Application\Providers\ControllerProvider;
use Igni\Http\Response;
use Igni\Http\Router\Route;

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

        $controllers->add(GoodbyeController::class);
    }
}
