<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Application;

use Igni\Application\Application;
use Igni\Application\Config;
use Igni\Application\ControllerAggregator;
use Igni\Application\Exception\ApplicationException;
use Igni\Application\Listeners\OnBootListener;
use Igni\Application\Providers\ControllerProvider;
use Igni\Tests\Fixtures\NullApplication;
use PHPUnit\Framework\TestCase;

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
            public function onBoot(Application $application): void
            {
                $application->onBoot = true;
            }
        });
        $application->extend(ApplicationModule::class);
        $application->run();

        self::assertTrue($application->onBoot);
        self::assertFalse($application->onRun);
        self::assertFalse($application->onShutDown);
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
    public function provideControllers(ControllerAggregator $controllers): void
    {
        $controllers->register(function() {}, 'test_controller');
    }
}
