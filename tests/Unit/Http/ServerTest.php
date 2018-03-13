<?php declare(strict_types=1);

namespace IgniTestFunctional\Http;

use Igni\Http\Server;
use Igni\Utils\TestCase;


final class ServerTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(Server::class, new Server());
    }
}
