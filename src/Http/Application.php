<?php declare(strict_types=1);

namespace Igni\Http;

use Igni\Application\Application as AbstractApplication;
use Igni\Application\Controller\ControllerAggregate as AbstractControllerAggregate;
use Igni\Application\Exception\ApplicationException;
use Igni\Http\Controller\ControllerAggregate;
use Igni\Http\Exception\HttpModuleException;
use Igni\Http\Middleware\CallableMiddleware;
use Igni\Http\Middleware\ErrorMiddleware;
use Igni\Http\Middleware\MiddlewarePipe;
use Igni\Http\Route as RouteInterface;
use Igni\Http\Router\Route;
use Igni\Http\Server\OnRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

/**
 * @see \Igni\Application\Application
 *
 * @package Igni\Http
 */
class Application
    extends AbstractApplication
    implements MiddlewareAggregate, MiddlewareInterface, RequestHandlerInterface, OnRequest {

    /**
     * @var Router
     */
    private $router;

    /**
     * @var ControllerAggregate
     */
    private $controllerAggregate;

    /**
     * @var string[]|MiddlewareInterface[]
     */
    private $middleware = [];

    /**
     * @var MiddlewarePipe
     */
    private $pipeline;

    /**
     * @var EmitterInterface
     */
    private $emitter;

    /**
     * Application constructor.
     *
     * @param ContainerInterface|null $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        parent::__construct($container);

        if ($this->getContainer()->has(Router::class)) {
            $this->router = $this->getContainer()->get(Router::class);
        } else {
            $this->router = new Router\Router();
        }

        if ($this->getContainer()->has(ControllerAggregate::class)) {
            $this->controllerAggregate = $this->getContainer()->get(ControllerAggregate::class);
        } else {
            $this->controllerAggregate = new ControllerAggregate($this->router);
        }

        if ($this->getContainer()->has(EmitterInterface::class)) {
            $this->emitter = $this->getContainer()->get(EmitterInterface::class);
        } else {
            $this->emitter = new SapiEmitter();
        }
    }

    /**
     * While testing call this method before handle method.
     */
    public function startup(): void
    {
        $this->handleOnBootListeners();
        $this->initialize();
        $this->handleOnRunListeners();
    }

    /**
     * While testing, call this method after handle method.
     */
    public function shutdown(): void
    {
        $this->handleOnShutDownListeners();
    }

    /**
     * Startups and run application with/or without dedicated server.
     * Once application is run it will listen to incoming http requests,
     * and takes care of the entire request flow process.
     *
     * @param Server|null $server
     */
    public function run(Server $server = null): void
    {
        $this->startup();
        if ($server) {
            $server->addListener($this);
            $server->start();
        } else {
            $response = $this->handle(ServerRequest::fromGlobals());
            $this->emitter->emit($response);
            if ($response instanceof Response) {
                $response->end();
            }
        }

        $this->shutdown();
    }

    /**
     * Registers PSR-15 compatible middelware.
     * Middleware can be either callable object which accepts PSR-7 server request interface and returns
     * response interface, or just class name that implements psr-15 middleware or its instance.
     *
     * @param MiddlewareInterface|callable $middleware
     */
    public function use($middleware): void
    {
        if (!is_subclass_of($middleware, MiddlewareInterface::class)) {
            if (!is_callable($middleware)) {
                throw new ApplicationException(sprintf(
                    'Middleware must be either class or object that implements `%s`',
                    MiddlewareInterface::class
                ));
            }

            $middleware = new CallableMiddleware($middleware);
        }

        $this->middleware[] = $middleware;
    }

    /**
     * Handles request flow process.
     *
     * @see MiddlewareInterface::process()
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        /** @var Route $route */
        $route = $this->router->findRoute(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        $controller = $route->getController();

        if ($request instanceof ServerRequest) {
            $request = $request->withAttributes($route->getAttributes());
        }

        if (is_string($controller) &&
            class_exists($controller) &&
            in_array(Controller::class, class_implements($controller))
        ) {
            /** @var Controller $instance */
            $instance = $this->resolver->resolve($controller);
            return $instance($request);
        }

        if (is_callable($controller)) {
            $response = $controller($request);
            if (!$response instanceof ResponseInterface) {
                throw HttpModuleException::controllerMustReturnValidResponse();
            }

            return $response;
        }

        throw HttpModuleException::couldNotRetrieveControllerForRoute($route->getPath());
    }

    /**
     * Runs application listeners and handles request flow process.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->getMiddlewarePipe()->handle($request);
        return $response;
    }

    /**
     * Decorator for handle method, used by server instance.
     * @see Application::handle()
     * @see Server::addListener()
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function onRequest(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }

    /**
     * Registers new controller that accepts get request
     * when request uri matches passed route pattern.
     *
     * @param string $route
     * @param callable $controller
     */
    public function get(string $route, callable $controller): void
    {
        $this->controllerAggregate->add($controller, Route::get($route));
    }

    /**
     * Registers new controller that accepts post request
     * when request uri matches passed route pattern.
     *
     * @param string $route
     * @param callable $controller
     */
    public function post(string $route, callable $controller): void
    {
        $this->controllerAggregate->add($controller, Route::post($route));
    }

    /**
     * Registers new controller that accepts put request
     * when request uri matches passed route pattern.
     *
     * @param string $route
     * @param callable $controller
     */
    public function put(string $route, callable $controller): void
    {
        $this->controllerAggregate->add($controller, Route::put($route));
    }

    /**
     * Registers new controller that accepts patch request
     * when request uri matches passed route pattern.
     *
     * @param string $route
     * @param callable $controller
     */
    public function patch(string $route, callable $controller): void
    {
        $this->controllerAggregate->add($controller, Route::patch($route));
    }

    /**
     * Registers new controller that accepts delete request
     * when request uri matches passed route pattern.
     *
     * @param string $route
     * @param callable $controller
     */
    public function delete(string $route, callable $controller): void
    {
        $this->controllerAggregate->add($controller, Route::delete($route));
    }

    /**
     * Registers new controller that accepts options request
     * when request uri matches passed route pattern.
     *
     * @param string $route
     * @param callable $controller
     */
    public function options(string $route, callable $controller): void
    {
        $this->controllerAggregate->add($controller, Route::options($route));
    }

    /**
     * Registers new controller that accepts head request
     * when request uri matches passed route pattern.
     *
     * @param string $route
     * @param callable $controller
     */
    public function head(string $route, callable $controller): void
    {
        $this->controllerAggregate->add($controller, Route::head($route));
    }

    /**
     * Registers new controller that listens on the passed route.
     *
     * @param RouteInterface $route
     * @param callable $controller
     */
    public function on(RouteInterface $route, callable $controller): void
    {
        $this->controllerAggregate->add($controller, $route);
    }

    /**
     * Returns application's controller aggregate.
     *
     * @return AbstractControllerAggregate
     */
    public function getControllerAggregate(): AbstractControllerAggregate
    {
        return $this->controllerAggregate;
    }

    protected function getMiddlewarePipe(): MiddlewarePipe
    {
        if ($this->pipeline) {
            return $this->pipeline;
        }

        return $this->pipeline = $this->composeMiddlewarePipe();
    }

    private function composeMiddlewarePipe(): MiddlewarePipe
    {
        $pipe = new MiddlewarePipe();
        $pipe->add(new ErrorMiddleware(function(Throwable $exception) {
            $this->handleOnErrorListeners($exception);
        }));
        foreach ($this->middleware as $middleware) {
            if (is_string($middleware)) {
                $middleware = $this->resolver->resolve($middleware);
            }
            $pipe->add($middleware);
        }
        $pipe->add($this);

        return $pipe;
    }
}
