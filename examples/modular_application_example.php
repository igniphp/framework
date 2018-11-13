<?php
require_once __DIR__.'/../vendor/autoload.php';

use Examples\Modules\SimpleModule;
use Igni\Application\HttpApplication;
use Igni\Network\Server\Configuration;
use Igni\Network\Server\HttpServer;


// Autoloader.
$classLoader = new Composer\Autoload\ClassLoader();
$classLoader->addPsr4('Examples\\Controllers\\', __DIR__ . '/Controllers');
$classLoader->addPsr4('Examples\\Modules\\', __DIR__ . '/Modules');
$classLoader->register();

// Create application instance.
$application = new HttpApplication();

// Attach modules.
$application->extend(SimpleModule::class);

// Run application.
if (php_sapi_name() == 'cli-server') {
    $application->run();
} else {
    $application->run(new HttpServer(new Configuration(8080, '0.0.0.0')));
}
