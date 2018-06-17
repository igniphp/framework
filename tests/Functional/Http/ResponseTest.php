<?php declare(strict_types=1);

namespace IgniTestFunctional\Http;

use Igni\Http\Response;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testFromJson(): void
    {
        $response = Response::fromJson(['test' => ['a' => 1]]);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{"test":{"a":1}}', (string) $response->getBody());
    }
}
