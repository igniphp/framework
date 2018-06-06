<?php declare(strict_types=1);

namespace IgniTestFunctional\Application;

use Igni\Application\Config;
use Igni\Application\Controller\ControllerAggregate;
use Igni\Application\Exception\ApplicationException;
use Igni\Application\Listeners\OnBootListener;
use Igni\Application\Providers\ControllerProvider;
use PHPUnit\Framework\TestCase;
use IgniTest\Fixtures\NullApplication;
use Igni\Application\Application;

final class ApplicationTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $application = new NullApplication();

        self::assertInstanceOf(Application::class, $application);
    }

    public function testExtend(): void
    {
        $application = new NullApplication();
        $application->extend(new class implements OnBootListener {
            public function onBoot(Application $application)
            {
                $application->onBoot = true;
            }
        });
        $application->extend(ApplicationModule::class);
        $application->run();

        self::assertTrue($application->onBoot);
        self::assertFalse($application->onRun);
        self::assertFalse($application->onShutDown);
        self::assertTrue($application->getControllerAggregate()->has('test_controller'));
    }

    public function testExtendWithInvalidModule(): void
    {
        $this->expectException(ApplicationException::class);
        $application = new NullApplication();
        $application->extend('t1');
    }

    public function testGetDefaultConfig(): void
    {
        $application = new NullApplication();
        self::assertInstanceOf(Config::class, $application->getConfig());
    }
}


class ApplicationModule implements ControllerProvider
{
    public function provideControllers(ControllerAggregate $controllers): void
    {
        $controllers->add(function() {}, 'test_controller');
    }
}
