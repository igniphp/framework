<?php declare(strict_types=1);

namespace Igni\Application;

use Igni\Application\Exception\ApplicationException;
use Igni\Container\ServiceLocator;
use Psr\Container\ContainerInterface;

/**
 * Factories application.
 */
final class ApplicationFactory
{
    private function __construct()
    {
    }

    /**
     * Factory method, for instantiating application from ini file.
     *
     * @param string $path
     * @return Application
     */
    public static function fromIni(string $path): Application
    {
        $config = Config::fromIni($path);

        // Build application instance from config
        if ($config->has('application')) {
            $applicationClass = $config->get('application.class');

            // Custom dependency Injection Container?
            $container = new ServiceLocator();
            if ($config->has('application.container')) {
                $containerClass = $config->get('application.container');
                if (!class_exists($containerClass)) {
                    throw new ApplicationException("Container class ${containerClass} could not be found. Did you forget to include it in your composer.json file?");
                }
                $container = new $containerClass;

                // Validate container
                if (!$container instanceof ContainerInterface) {
                    throw new ApplicationException("Container ${containerClass} is not valid psr container.");
                }
            }

            $instance = new $applicationClass($container);

            // Load modules
            if ($config->has('application.modules')) {
                foreach ($config->get('application.modules') as $module) {
                    $instance->extend($module);
                }
            }

            // Load middleware for http application
            if ($instance instanceof \Igni\Http\Application && $config->has('application.middleware')) {
                foreach ($config->get('application.middleware') as $middleware) {
                    $instance->use($middleware);
                }
            }

        } else {
            throw new ApplicationException("Cannot create application, check for existence of [application:*] or [application] section in your ini file ${path}");
        }

        return $instance;
    }
}
