<?php declare(strict_types=1);

namespace IgniTestFunctional\Application;

use Igni\Application\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $config = new Config();
        self::assertInstanceOf(Config::class, $config);
    }

    public function testThatConfigUnderstandsGlobalConstants(): void
    {
        $value = '12345';
        define('TEST_1', $value);

        $config = new Config([
            'test' => '${TEST_1}',
        ]);

        self::assertSame($value, $config->get('test'));
    }

    public function testNestingValues(): void
    {
        $config = new Config();

        $config->set('test.a', 1);
        self::assertEquals(['a' => 1], $config->get('test'));
        self::assertSame(1, $config->get('test.a'));
    }

    public function testConfigMerge(): void
    {
        $a = new Config([
           'testA' => [
               'a' => 1,
           ],
           'testB' => 'b'
        ]);

        $b = new Config([
            'testA' => [
                'b' => 2
            ],
            'testB' => 'c',
            'testC' => 2
        ]);

        $a->merge($b);

        self::assertSame(
            [
                'testA' => [
                    'a' => 1,
                    'b' => 2
                ],
                'testB' => ['b', 'c'],
                'testC' => 2,
            ],
            $a->toArray()
        );
    }

    public function testToArray(): void
    {
        $config = new Config();
        $config->set('a.b.c' , 1);
        $config->set('b.c', 2);
        $config->set('c', 3);

        self::assertSame([
            'a' => [
                'b' => [
                    'c' => 1,
                ],
            ],
            'b' => [
                'c' => 2,
            ],
            'c' => 3,
        ], $config->toArray());
    }

}
