<?php declare(strict_types=1);

namespace Igni\Application;

use Igni\Application\Controller\ControllerAggregate;
use Igni\Application\Exception\ApplicationException;
use Igni\Application\Listeners\OnBootListener;
use Igni\Application\Listeners\OnErrorListener;
use Igni\Application\Listeners\OnRunListener;
use Igni\Application\Listeners\OnShutDownListener;
use Igni\Application\Providers\ConfigProvider;
use Igni\Application\Providers\ControllerProvider;
use Igni\Application\Providers\ServiceProvider;
use Igni\Container\DependencyResolver;
use Igni\Container\ServiceLocator;
use Igni\Http;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * Main glue between all components.
 *
 * @package Igni\Application
 */
abstract class Application
{
    /**
     * @var ServiceLocator|ContainerInterface
     */
    protected $serviceLocator;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var object[]|class[]
     */
    protected $modules;

    /**
     * @var DependencyResolver
     */
    protected $dependencyResolver;

    /**
     * Application constructor.
     *
     * @param ContainerInterface|null $container
     * @param Config|null $config
     */
    public function __construct(ContainerInterface $container = null, Config $config = null)
    {
        $this->serviceLocator = $container ?? new ServiceLocator();
        $this->config = $config ?? new Config([]);

        $this->dependencyResolver = new DependencyResolver($this->serviceLocator);
        $this->modules = [];
    }

    /**
     * Allows for application extension by modules.
     * Module can be any valid object or class name.
     *
     * @param $module
     */
    public function extend($module): void
    {
        if (is_object($module) || class_exists($module)) {
            $this->modules[] = $module;
        } else {
            throw ApplicationException::forInvalidModule($module);
        }
    }

    /**
     * Starts the application.
     * Initialize modules. Performs tasks to generate response for the client.
     *
     * @return mixed
     */
    abstract public function run();

    /**
     * @return ControllerAggregate
     */
    abstract public function getControllerAggregate(): ControllerAggregate;

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
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

            // Custom Dependecy Injection Container?
            $container = new ServiceLocator();
            if ($config->has('application.container')) {
                $containerClass = $config->get('application.container');
                if (!class_exists($containerClass)) {
                    throw new ApplicationException("Container class ${containerClass} could not be found. Did you forget to include it in your composer.json file?");
                }
                $container = new $containerClass;
            }

            // Validate container
            if (!$container instanceof ContainerInterface) {
                throw new ApplicationException("Container ${containerClass} is not valid psr container.");
            }

            $instance = new $applicationClass($container);

            // Load modules
            if ($config->has('application.modules')) {
                foreach ($config->get('application.modules') as $module) {
                    $instance->extend($module);
                }
            }

            // Load middlewares for http application
            if ($instance instanceof Http\Application && $config->has('application.middlewares')) {

            }

        } else {
            throw new ApplicationException("Cannot create application, check for existence of [application:*] or [application] section in your ini file ${path}");
        }

        return $instance;
    }


    protected function handleOnBootListeners(): void
    {
        foreach ($this->modules as $module) {
            if ($module instanceof OnBootListener) {
                $module->onBoot($this);
            }
        }
    }

    protected function handleOnShutDownListeners(): void
    {
        foreach ($this->modules as $module) {
            if ($module instanceof OnShutDownListener) {
                $module->onShutDown($this);
            }
        }
    }

    protected function handleOnErrorListeners(Throwable $exception): void
    {
        foreach ($this->modules as $module) {
            if ($module instanceof OnErrorListener) {
                $module->onError($this, $exception);
            }
        }
    }

    protected function handleOnRunListeners(): void
    {
        foreach ($this->modules as $module) {
            if ($module instanceof OnRunListener) {
                $module->onRun($this);
            }
        }
    }

    protected function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        foreach ($this->modules as &$module) {
            $this->initializeModule($module);
        }

        $this->initialized = true;
    }

    protected function initializeModule(&$module): void
    {
        if (is_string($module)) {
            $module = $this->dependencyResolver->resolve($module);
        }

        if ($module instanceof ConfigProvider) {
            $module->provideConfig($this->config);
        }

        if ($module instanceof ControllerProvider) {
            $module->provideControllers($this->getControllerAggregate());
        }

        if ($module instanceof ServiceProvider) {
            $module->provideServices($this->serviceLocator);
        }
    }
}
