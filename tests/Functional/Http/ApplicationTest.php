<?php declare(strict_types=1);

namespace IgniTestFunctional\Http;

use Igni\Application\Application as AbstractApplication;
use Igni\Application\Listeners\OnBootListener;
use Igni\Application\Listeners\OnRunListener;
use Igni\Application\Listeners\OnShutDownListener;
use Igni\Http\Application;
use Igni\Http\Controller\ControllerAggregate;
use Igni\Http\Request;
use Igni\Http\Response;
use Igni\Http\ServerRequest;
use Igni\Http\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApplicationTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $application = new Application();
        self::assertInstanceOf(Application::class, $application);
    }

    public function testProcessRequestWithNoRoutes(): void
    {
        $application = new Application();
        $request = ServerRequest::fromUri('/test/1');
        $response = $application->handle($request);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertContains('No resource found', (string) $response->getBody());
    }

    public function testProcessRequestWithClosures(): void
    {
        $body = ['test' => 1];
        $application = new Application();
        $application->get('/test/{id}', function(ServerRequest $request): ResponseInterface {
            return Response::fromJson(['test' => (int) $request->getAttribute('id')]);
        });
        $request = ServerRequest::fromUri('/test/1');
        $response = $application->handle($request);

        self::assertEquals($body, json_decode((string)$response->getBody(), true));
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testProcessWithMiddleware(): void
    {
        $body = 'No content available.';
        $application = new Application();
        $application->get('/test/{id}', function(ServerRequest $request): ResponseInterface {
            return Response::fromJson(['test' => (int) $request->getAttribute('id')]);
        });

        $application->use(function(ServerRequestInterface $request, RequestHandlerInterface $next) use ($body) {
            $response = $next->handle($request);

            return $response->withBody(Stream::fromString($body));
        });

        $request = ServerRequest::fromUri('/test/1');
        $response = $application->handle($request);

        self::assertEquals($body, (string) $response->getBody());
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testListeners(): void
    {
        $onBoot = new class implements OnBootListener {
            public $dispatched = false;
            public function onBoot(AbstractApplication $application)
            {
                $this->dispatched = true;
            }
        };
        $onRun = new class implements OnRunListener {
            public $dispatched = false;
            public function onRun(AbstractApplication $application)
            {
                $this->dispatched = true;
            }
        };
        $onShutDown = new class implements OnShutDownListener {
            public $dispatched = false;
            public function onShutDown(AbstractApplication $application)
            {
                $this->dispatched = true;
            }
        };
        $application = new Application();
        $application->extend($onRun);
        $application->extend($onShutDown);
        $application->extend($onBoot);
        $application->startup();
        $application->handle(ServerRequest::fromUri('/test'));
        $application->shutdown();
        self::assertTrue($onRun->dispatched);
        self::assertTrue($onShutDown->dispatched);
        self::assertTrue($onBoot->dispatched);
    }

    /**
     * @dataProvider getApplicationRouteMethods
     */
    public function testRoutes(string $type, string $requestMethod): void
    {

        $application = new Application();
        $application->$type('/test/{name}', function(ServerRequest $request): Response {
            return Response::fromText("Test passes: {$request->getAttribute('name')}");
        });
        $application->startup();
        $response = $application->handle(ServerRequest::fromUri('/test/OK', $requestMethod));

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame("Test passes: OK", (string) $response->getBody());
    }

    public function testGetControllerAggregate(): void
    {
        $application = new Application();

        self::assertInstanceOf(ControllerAggregate::class, $application->getControllerAggregate());
    }

    public function getApplicationRouteMethods(): array
    {
        return  $routes = [
            ['post', Request::METHOD_POST],
            ['get', Request::METHOD_GET],
            ['delete', Request::METHOD_DELETE],
            ['put', Request::METHOD_PUT],
            ['patch', Request::METHOD_PATCH],
            ['options', Request::METHOD_OPTIONS],
            ['head', Request::METHOD_HEAD],
        ];
    }
}
