<?php declare(strict_types=1);

namespace IgniTest\Fixtures;

use Igni\Application\Application;
use Igni\Application\Controller;
use Igni\Application\Controller\ControllerAggregate;
use Igni\Container\ServiceLocator;

class NullApplication extends Application
{
    public $onBoot = false;
    public $onShutDown = false;
    public $onRun = false;

    public function run(): void
    {
        $this->handleOnBootListeners();
        $this->initialize();
        $this->handleOnRunListeners();
        $this->handleOnShutDownListeners();
    }

    public function getControllerAggregate(): ControllerAggregate
    {
        static $aggregate;

        if (null !== $aggregate) {
            return $aggregate;
        }
        $locator = $this->serviceLocator;

        return $aggregate = new class($locator) implements ControllerAggregate {

            /**
             * @var ServiceLocator
             */
            private $locator;

            public function __construct(ServiceLocator $locator)
            {
                $this->locator = $locator;
            }

            public function add($controller, string $name = null): void
            {
                if ($controller) {
                    $this->locator->set($name, $controller);
                } else {
                    $this->locator->share($name);
                }
            }

            public function has(string $controller): bool
            {
                return $this->locator->has($controller);
            }

            public function get(string $controller): Controller
            {
                return $this->locator->get($controller);
            }
        };
    }
}
