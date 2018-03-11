<?php declare(strict_types=1);

namespace Igni\Application;

use Igni\Application\Exception\ApplicationException;
use Igni\Application\Exception\ConfigException;
use Igni\Container\ServiceLocator;
use Igni\Http\HttpModule;

/**
 * Factories different types of application
 */
final class ApplicationFactory
{
    private function __construct()
    {
    }

    public static function fromConfig(string $config): Application
    {
        $config = Config::from($config);
        $serviceLocator = new ServiceLocator();
        $serviceLocator['config'] = $config;
        $application = new Octopus($serviceLocator);
        $application->addModule(HttpModule::class);

        if ($config->has('application.modules')) {
            $modules = (array) $config->get('application.modules');
            foreach ($modules as $module) {
                $application->addModule($module);
            }
        }

        if ($config->has('config.dir')) {
            $configDir = getcwd() . DIRECTORY_SEPARATOR . $config->get('config.dir');
            if (!is_dir($configDir)) {
                throw ConfigException::forUnreadableDirectory($configDir);
            }

            $pattern = '*';
            if ($config->has('config.pattern')) {
                $pattern = $config->get('config.pattern');
            }

            $config = new Config();
            $glob = $configDir . DIRECTORY_SEPARATOR . $pattern;
            foreach (glob($glob, GLOB_BRACE) as $file) {
                $config->merge(Config::from($file));
            }
        }

        return $application;
    }
}
