<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Application;

use Igni\Application\Config;
use Igni\Application\Exception\ConfigException;
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

    public function testExtract(): void
    {
        $config = new Config();
        $config->set('a.b.c' , 123);
        $config->set('a.a.b', 112);
        $config->set('a.a.c', 113);
        $config->set('a.c.a', 131);
        $config->set('b.c', 23);
        $config->set('c', 3);

        self::assertSame(
            [
                'b.c' => 123,
                'a.b' => 112,
                'a.c' => 113,
                'c.a' => 131,
            ],
            $config->extract('a')->toFlatArray()
        );

        self::assertSame(
            [
                'b' => 112,
                'c' => 113,
            ],
            $config->extract('a.a')->toFlatArray()
        );

        self::assertSame(
            [
                'c' => 23,
            ],
            $config->extract('b')->toFlatArray()
        );
    }

    public function testFailOnExtractingNonArrayKey(): void
    {
        $this->expectException(ConfigException::class);
        $config = new Config();
        $config->set('c', 3);
        self::assertSame(
            [
                3,
            ],
            $config->extract('c')->toFlatArray()
        );
    }

    public function testToFlatArray(): void
    {
        $config = new Config();
        $config->set('a.b.c' , 1);
        $config->set('b.c', 2);
        $config->set('b.d', 2.1);
        $config->set('c', 3);

        self::assertSame(
            [
                'a.b.c' => 1,
                'b.c' => 2,
                'b.d' => 2.1,
                'c' => 3,
            ],
            $config->toFlatArray()
        );
    }
}
