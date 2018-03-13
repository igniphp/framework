<?php declare(strict_types=1);

namespace IgniTestFunctional\Application;

use Igni\Application\Controller\ControllerAggregate;
use Igni\Application\Listeners\OnBootListener;
use Igni\Application\Providers\ControllerProvider;
use Igni\Utils\TestCase;
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
}


class ApplicationModule implements ControllerProvider
{
    public function provideControllers(ControllerAggregate $controllers): void
    {
        $controllers->add(function() {}, 'test_controller');
    }
}
