<?php
require_once __DIR__.'/../vendor/autoload.php';

use Igni\Http\Application;
use Igni\Http\Server;
use Examples\Modules\SimpleModule;

// Autoloader.
$classLoader = new Composer\Autoload\ClassLoader();
$classLoader->addPsr4('Examples\\Controllers\\', __DIR__ . '/Controllers');
$classLoader->addPsr4('Examples\\Modules\\', __DIR__ . '/Modules');
$classLoader->register();

// Create application instance.
$application = new Application();

// Attach modules.
$application->extend(SimpleModule::class);

// Run application.
if (php_sapi_name() == 'cli-server') {
    $application->run();
} else {
    $application->run(new Server());
}
