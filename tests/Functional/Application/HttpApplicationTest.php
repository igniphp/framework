<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Application;

use Exception;
use Igni\Application\Application;
use Igni\Application\ControllerAggregator;
use Igni\Application\Exception\ApplicationException;
use Igni\Application\HttpApplication;
use Igni\Application\Listeners\OnBootListener;
use Igni\Application\Listeners\OnErrorListener;
use Igni\Application\Listeners\OnRunListener;
use Igni\Application\Listeners\OnShutDownListener;
use Igni\Container\ServiceLocator;
use Igni\Network\Exception\HttpException;
use Igni\Network\Http\Request;
use Igni\Network\Http\Response;
use Igni\Network\Http\Route;
use Igni\Network\Http\Router;
use Igni\Network\Http\ServerRequest;
use Igni\Network\Http\Stream;
use Igni\Tests\Fixtures\HttpController;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final class HttpApplicationTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $application = new HttpApplication();
        self::assertInstanceOf(HttpApplication::class, $application);
    }

    public function testProcessRequestWithNoRoutes(): void
    {
        $application = new HttpApplication();
        $request = new ServerRequest('/test/1');
        $response = $application->handle($request);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertContains('No route matches requested uri', (string) $response->getBody());
    }

    public function testProcessRequestWithClosures(): void
    {
        $body = ['test' => 1];
        $application = new HttpApplication();
        $application->get('/test/{id}', function(ServerRequest $request): ResponseInterface {
            return Response::asJson(['test' => (int) $request->getAttribute('id')]);
        });
        $request = new  ServerRequest('/test/1');
        $response = $application->handle($request);

        self::assertEquals($body, json_decode((string)$response->getBody(), true));
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testErrorListener(): void
    {
        $exceptionMock = new class extends Exception implements HttpException {
            public function toResponse(): ResponseInterface
            {
                $responseMock = Mockery::mock(ResponseInterface::class);
                $responseMock->shouldReceive('getStatusCode')
                    ->andReturn(Response::HTTP_I_AM_A_TEAPOT);

                $responseMock->shouldReceive('getBody')
                    ->andReturn(Stream::fromString('Override exception'));

                return $responseMock;
            }
        };

        $application = new HttpApplication();
        $application->extend(new class($exceptionMock) implements OnErrorListener {
            private $exceptionMock;

            public function __construct(HttpException $exception)
            {
                $this->exceptionMock = $exception;
            }

            public function onError(Application $application, Throwable $exception): Throwable
            {
                return $this->exceptionMock;
            }
        });
        $request = new  ServerRequest('/test/1');
        $response = $application->handle($request);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame(Response::HTTP_I_AM_A_TEAPOT, $response->getStatusCode());
        self::assertContains('Override exception', (string) $response->getBody());

    }

    public function testProcessWithMiddleware(): void
    {
        $body = 'No content available.';
        $application = new HttpApplication();
        $application->get('/test/{id}', function(ServerRequest $request): ResponseInterface {
            return Response::asJson(['test' => (int) $request->getAttribute('id')]);
        });

        $application->use(function(ServerRequestInterface $request, RequestHandlerInterface $next) use ($body) {
            $response = $next->handle($request);

            return $response->withBody(Stream::fromString($body));
        });

        $request = new ServerRequest('/test/1');
        $response = $application->handle($request);

        self::assertEquals($body, (string) $response->getBody());
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testRegisterCallableController(): void
    {
        $controller = function() {};
        $route = Mockery::mock(Route::class);
        $route->shouldReceive('withController')
            ->withArgs([$controller]);
        $route->shouldReceive('getPath')
            ->andReturn('test/path');

        $router = Mockery::mock(Router::class);
        $router->shouldReceive('add')
            ->withArgs(function($route) {
                self::assertInstanceOf(Route::class, $route);
                return true;
            });
        $container = new ServiceLocator();
        $container->share(Router::class, function() use ($router) {
            return $router;
        });
        $aggregate = new HttpApplication($container);

        self::assertNull($aggregate->register($controller, $route));
    }

    public function testRegisterControllerClass(): void
    {
        $controller = HttpController::class;

        $router = Mockery::mock(Router::class);
        $router->shouldReceive('add')
            ->withArgs(function(Route $route) {
                self::assertSame(HttpController::URI, $route->getPath());
                return true;
            });
        $container = new ServiceLocator();
        $container->share(Router::class, function() use ($router) {
            return $router;
        });
        $aggregate = new HttpApplication($container);

        self::assertNull($aggregate->register($controller));
    }

    public function testRegisterControllerObject(): void
    {
        $controller = new HttpController();

        $router = Mockery::mock(Router::class);
        $router->shouldReceive('add')
            ->withArgs(function(Route $route) {
                self::assertInstanceOf(Route::class, $route);
                self::assertSame(HttpController::URI, $route->getPath());
                return true;
            });
        $container = new ServiceLocator();
        $container->share(Router::class, function() use ($router) {
            return $router;
        });
        $aggregate = new HttpApplication($container);

        self::assertNull($aggregate->register($controller));
    }

    public function testRegisterInvalidController(): void
    {
        $this->expectException(ApplicationException::class);
        $aggregate = new HttpApplication();
        $aggregate->register(1);
    }

    public function testProcessWithCallableMiddlewareHandler(): void
    {
        $body = 'No content available.';
        $application = new HttpApplication();
        $application->get('/test/{id}', function(ServerRequest $request): ResponseInterface {
            return Response::asJson(['test' => (int) $request->getAttribute('id')]);
        });

        $application->use(function(ServerRequestInterface $request, $next) use ($body) {
            $response = $next($request);

            return $response->withBody(Stream::fromString($body));
        });

        $request = new ServerRequest('/test/1');
        $response = $application->handle($request);

        self::assertEquals($body, (string) $response->getBody());
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testListeners(): void
    {
        $onBoot = new class implements OnBootListener {
            public $dispatched = false;
            public function onBoot(Application $application): void
            {
                $this->dispatched = true;
            }
        };
        $onRun = new class implements OnRunListener {
            public $dispatched = false;
            public function onRun(Application $application): void
            {
                $this->dispatched = true;
            }
        };
        $onShutDown = new class implements OnShutDownListener {
            public $dispatched = false;
            public function onShutDown(Application $application): void
            {
                $this->dispatched = true;
            }
        };
        $application = new HttpApplication();
        $application->extend($onRun);
        $application->extend($onShutDown);
        $application->extend($onBoot);
        $application->startup();
        $application->handle(new ServerRequest('/test'));
        $application->shutdown();
        self::assertTrue($onRun->dispatched);
        self::assertTrue($onShutDown->dispatched);
        self::assertTrue($onBoot->dispatched);
    }

    /**
     * @dataProvider provideTestRoutes
     */
    public function testRoutes(string $type, string $requestMethod): void
    {

        $application = new HttpApplication();
        $application->$type('/test/{name}', function(ServerRequest $request): Response {
            return Response::asText("Test passes: {$request->getAttribute('name')}");
        });
        $application->startup();
        $response = $application->handle(new ServerRequest('/test/OK', $requestMethod));

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('Test passes: OK', (string) $response->getBody());
    }

    public function testGetControllerAggregator(): void
    {
        $application = new HttpApplication();

        self::assertInstanceOf(ControllerAggregator::class, $application->getControllerAggregator());
    }

    public function provideTestRoutes(): array
    {
        return [
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
