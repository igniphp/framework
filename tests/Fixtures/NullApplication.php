<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures;

use Igni\Application\Application;
use Igni\Application\Controller;
use Igni\Application\ControllerAggregator;
use Igni\Application\Http\MiddlewareAggregator;
use Igni\Application\HttpApplication;
use Igni\Container\ServiceLocator;
use Psr\Http\Server\MiddlewareInterface;

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

    public function getControllerAggregator(): ControllerAggregator
    {
        static $aggregate;

        if (null !== $aggregate) {
            return $aggregate;
        }
        $locator = $this->getContainer();

        return $aggregate = new class($locator) implements ControllerAggregator {

            /**
             * @var ServiceLocator
             */
            private $locator;

            public function __construct(ServiceLocator $locator)
            {
                $this->locator = $locator;
            }

            public function register($controller, string $name = null): void
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

    /**
     * Middleware aggregator is used to register application's middlewares.
     *
     * @return MiddlewareAggregator
     */
    public function getMiddlewareAggregator(): MiddlewareAggregator
    {
        static $aggregate;

        if (null !== $aggregate) {
            return $aggregate;
        }

        return $aggregate = new class implements MiddlewareAggregator {

            public $middleware = [];

            /**
             * @param string|MiddlewareInterface|callable $middleware
             */
            public function use($middleware): void
            {
                $this->middleware[] = $middleware;
            }
        };
    }
}
