# ![Igni logo](https://github.com/igniphp/common/blob/master/logo/full.svg)![Build Status](https://travis-ci.org/igniphp/framework.svg?branch=master)

## Igni framework
Licensed under MIT License.

## Table of Contents
 * [Overview](#overview)
    + [Introduction](#introduction)
    + [Installation](#installation)
    + [Usage](#usage)
  * [Routing](#routing)
  * [Middleware](#middleware)
  * [Modules](#modules)
    + [Listeners](#listeners)
    + [Providers](#providers)
  * [The Request](#the-request)
  * [The Response](#the-response)
  * [Error handling](#error-handling)
  * [Testing](#testing)
  * [Working with containers](#working-with-containers)
  * [Igni's server](#ignis-server)
    + [Installation](#installation-1)
    + [Basic Usage](#basic-usage)
    + [Listeners](#listeners-1)
    + [Configuration](#configuration)
  * [Webserver configuration](#external-webserver)
    + [Apache](#apache)
    + [nginx + php-fpm](#nginx--php-fpm)
    + [PHP built-in webserver](#php-built-in-webserver)

## Overview

### Introduction
**Igni** anti-framework allows you to write extensible and middleware based REST applications which are [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant.

Igni aims to be:
 - Lightweight: Igni's source code is around 60KB.
 - Extensible: Igni offers extension system that allows to use `PSR-15` middleware (with `zend-stratigility`) and modular architecture.
 - Testable: Igni extends `zend-diactoros` (`PSR-7` implementation) and allows to manually dispatch routes to perform end-to-end tests.
 - Easy to use: Igni exposes an intuitive and concise API. All you need to do to use it is to include composer autoloader.
 - Transparent: dont bind your application to the framework.

### Installation

``` 
composer install igniphp/framework
```

### Usage

In a nutshell, you can define controllers and map them to routes in one step. When the route matches, the function is executed and the response is dispatched to the client.

Igni can be used with already configured web-server or with shipped server (if `swoole` extension is installed).

Following example shows how you can use Igni without build in webserver:
```php
<?php
require_once __DIR__.'/vendor/autoload.php';

use Igni\Application\HttpApplication;
use Igni\Network\Http\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

// Setup application
$application = new HttpApplication();

// Define routing
$application->get('/hello/{name}', function (ServerRequestInterface $request): ResponseInterface {
    return Response::asText("Hello {$request->getAttribute('name')}");
});

// Run the application
$application->run();
```
First composer's autoloader is included in the application. Next instantiation of application happens. 
In line `64` callable controller is attached to the `GET /hello/{name}` route.
Finally application is being run.

Similar approach is taken when build-in server comes in place:

```php
<?php
require_once __DIR__.'/vendor/autoload.php';

use Igni\Application\HttpApplication;
use Igni\Network\Http\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Igni\Network\Server\HttpServer;

// Setup application and routes
$application = new HttpApplication();
$application->get('/hello/{name}', function (ServerRequestInterface $request): ResponseInterface {
    return Response::asText("Hello {$request->getAttribute('name')}");
});

// Run with the server
$application->run(new HttpServer());
```

Server instance is created and passed to application's `run` method.
While using default settings it listens on incoming localhost connections on port `8080`.

## Routing

The route is a pattern representation of expected URI requested by clients.
Route pattern can use a syntax where `{var}` specifies a placeholder with name `var` and
it matches a regex `[^/]+.`. 

```php
<?php
$application->get('/users/{id}', function() {...});
$application->get('/books/{isbn}/page/{page}', function() {...});
```

### Custom pattern

You can specify a custom pattern after the parameter name, the pattern should be enclosed between `<` and `>`.
Here are some examples:  

```php
<?php
use Igni\Network\Http\Route;

// Matches following get requests: /users/42, /users/1, but not /users/me 
$application->get('/users/{id<\d+>}', function() {...});

// Matches following get requests: /users/42, /users/me
$application->get('/users/{name<\w+>}', function() {...});

// Bind controller to custom route instance
$application->on(new Route('/hello/{name<\w+>}', ['GET', 'DELETE', 'POST']), function() {...});
```

### Default value

Default value can be specified after parameter name and should follow `?` sign, example:
```php
<?php

// Matches following get requests with default id(2): /users/42, /users 
$application->get('/users/{id<\d+>?2}', function() {...});

// Matches following get requests with default id(me): /users/42, /users/me
$application->get('/users/{name<\w+>?me}', function() {...});
```

### Optional parameter

In order to make parameter optional just add `?` add the end of the name
```php
<?php

// Matches following get requests: /users/42, /users 
$application->get('/users/{id?}', function() {...});
```

    
Full example:
```php
<?php
// Include composer's autoloader.
require_once __DIR__.'/vendor/autoload.php';

use Igni\Application\HttpApplication;
use Igni\Network\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

$application = new HttpApplication();
$application->get('/hello/{name}', function (ServerRequestInterface $request) {
    return Response::asText("Hello: {$request->getAttribute('name')}");
});
$application->run();
```

#### `HttpApplication::on(Route $route, callable $controller)`

Makes `$controller` to listen on instance of the `$route`. 

#### `HttpApplication::get(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `GET` request. 

Should be used to **read** or **retrieve** resource. On success response should return `200` (OK) status code. 
In case of error most often `404` (Not found) or `400` (Bad request) should be returned in the status code.

#### `HttpApplication::post(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `POST` request.

Should be used to **create** new resources (can be also used as a wild card verb for operations that don't fit elsewhere). 
On successful creation, response should return `201` (created) or `202` (accepted) status code.
In case of error most often `406` (not acceptable), `409` (conflict) `413` (request entity too large)

#### `HttpApplication::put(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `PUT` request.

Should be used to **update** a specific resource (by an identifier) or a collection of resources. 
Can also be used to create a specific resource if the resource identifier is known before-hand.
Response code scenario is same as `post` method with additional `404` if resource to update was not found.

#### `HttpApplication::patch(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `PATCH` request.

As `patch` should be used for **modify** resource. The difference is that `patch` request 
can contain only the changes to the resource, not the complete resource as `put` or `post`.

#### `HttpApplication::delete(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `DELETE` request.

Should be used to **delete** resource.

#### `HttpApplication::options(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `OPTIONS` request.

#### `HttpApplication::head(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `HEAD` request.

## Middleware

Middleware is an individual component participating in processing incoming request and the creation
of resulting response.

Middleware can be used to:
 - Handle authentication details
 - Perform content negotiation
 - Error handling

In Igni middleware can be any callable that accepts `\Psr\Http\Message\ServerRequestInterface` and `\Psr\Http\Server\RequestHandlerInterface` or `callable` as parameters 
and returns valid instance of `\Psr\Http\Message\ResponseInterface` or any class/object that implements `\Psr\Http\Server\MiddlewareInterface` interface.

You can add as many middleware as you want, and they are triggered in the same order as you add them. 
In fact even `Igni\Http\HttpApplication` is a middleware itself which is automatically added at the end of the pipe.

#### Example
```php
<?php
// Include composer's autoloader.
require_once __DIR__.'/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Igni\Application\HttpApplication;

class BenchmarkMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface 
    {
        $time = microtime(true);
        // $response = $next($request); or the psr-15 way:
        $response = $next->handle($request);
        $renderTime = microtime(true) - $time;
        
        return $response->withHeader('render-time', $renderTime);
    }
}

$application = new HttpApplication();

// Attach custom middleware instance.
$application->use(new BenchmarkMiddleware());

// Attach callable middleware.
$application->use(function($request, callable $next) {
    $response = $next($request);
    return $response->withHeader('foo', 'bar');
});

// Run the application.
$application->run();
```

## Modules

Module is a reusable part of application or business logic. It can listen on application state or/and extend application 
by providing additional middleware, services, models, libraries etc...

In Igni module is any class implementing any listener or provider interface.

The following list contains all possible interfaces that module can implement in order to provide additional 
features for the application:

 - Listeners:
    - `Igni\Application\Listeners\OnBootListener`
    - `Igni\Application\Listeners\OnErrorListener`
    - `Igni\Application\Listeners\OnRunListener`
    - `Igni\Application\Listeners\OnShutdownListener`
 - Providers:
    - `Igni\Application\Providers\ConfigProvider` 
    - `Igni\Application\Providers\ControllerProvider` 
    - `Igni\Application\Providers\ServiceProvider` 
    - `Igni\Application\Providers\MiddlewareProvider` 

Example:
```php
<?php
require_once __DIR__.'/vendor/autoload.php';

use Igni\Application\ControllerAggregator;
use Igni\Application\Providers\ControllerProvider;
use Igni\Network\Http\Response;
use Igni\Network\Http\Route;
use Igni\Application\HttpApplication;

/**
 * Module definition.
 */
class SimpleModule implements ControllerProvider
{
    public function provideControllers(ControllerAggregator $controllers): void
    {
        // Add controller that greets client when /hello/{name} URI is requested
        $controllers->register(function ($request) {
            return Response::asText("Hello {$request->getAttribute('name')}!");
        }, Route::get('/hello/{name}'));
    }
}

$application = new HttpApplication();

// Extend application with the module.
$application->extend(SimpleModule::class);

// Run the application.
$application->run();
```

### Listeners

#### Boot Listener
OnBootListener can be implemented to perform tasks on application in boot state.

#### Run Listener
OnRunListener can be implemented to perform tasks which are dependent on various services
provided by extensions.

#### Shutdown Listener
Can be used for cleaning-up tasks.

### Providers

#### Config Provider
Config provider is used to provide additional configuration settings to config service `\Igni\Application\Config`.  

#### Controller Provider
Controller provider is used to register controllers within the application.  

#### Service Provider
Makes usage of PSR compatible DI of your choice (If none is passed to application igniphp/container 
implementation will be used as default) to register additional services.

## Controllers

Igni uses invokable controllers (single action controllers). There few reasons for that:

- Controller can effectively wrap simple functionality into well defined namespace
- Less dependencies are required to perform the action 
- Can be easy replaced with functions
- Does not break SRP 
- Makes testing easier

In order to define controller one must implement `Igni\Http\Controller` interface.
The following code contains an example controller which contains welcome message in the response.

```php
<?php
// Include composer's autoloader.
require_once __DIR__.'/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Igni\Network\Http\Route;
use Igni\Application\Http\Controller;
use Igni\Network\Http\Response;

class WelcomeUserController implements Controller
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface 
    {
        return Response::asText("Hi {$request->getAttribute('name')}!");
    }
    
    public static function getRoute(): Route 
    {
        return Route::get('hi/{name}');
    }
}
```

Controller can be registered either in your [module file](../examples/Modules/SimpleModule.php) 
or simply by calling `register` method on application's controller aggregate:

```php
$application->getControllerAggregate()->add(WelcomeUserController::class);
```

## The Request
Igni's controllers and middleware are given a PSR-7 server request object that represents http request send 
by the client. Request contains route's params, body, request method, request uri and so on.

For information how to work with PSR-7 [read this](https://www.php-fig.org/psr/psr-7/). 

## The Response
Igni's controllers and middleware must return valid PSR-7 response object. 
Igni's `Igni\Network\Http\Response` class provides factories methods to simplify response creation.

#### `Response::empty(int $status = 200, array $headers = [])`

Creates empty PSR-7 response object.

#### `Response::asText(string $text, int $status = 200, array $headers = [])`

Creates PSR-7 request with content type set to `text/plain` and body containing passed `$text`

#### `Response::asJson($data, int $status = 200, array $headers = [])`

Creates PSR-7 request with content type set to `application/json` and body containing json data.
`$data` can be array or `\JsonSerializable` instance.

#### `Response::asHtml(string $html, int $status = 200, array $headers = [])`

Creates PSR-7 request with content type set to `text/html` and body containing passed html.

#### `Response::asXml($data, int $status = 200, array $headers = [])`

Creates PSR-7 request with content type set to `application/xml` and body containing xml string.
`$data` can be `\SimpleXMLElement`, `\DOMDocument` or just plain string.

## Error handling
Igni provides default error handler (`\Igni\Network\Http\Middleware\ErrorMiddleware`) so if anything goes 
wrong in your application the error will not be directly propagated to the client layer unless it
is a fatal error (fatals cannot be catched nor handled).

If you intend to propagate custom error for your clients, you have two options:
- Custom exception classes implementing `\Igni\Network\Exception\HttpException`
- Provide custom error handling middleware

### Custom Exceptions 
All exceptions that implement `\Igni\Network\Exception\HttpException` are catch by default error handler and used
to generate response for your clients:

```php
<?php
// Include composer's autoloader.
require_once __DIR__.'/vendor/autoload.php';

use Igni\Network\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Igni\Network\Http\Response;
use Igni\Application\HttpApplication;

class NotFoundException extends \RuntimeException implements HttpException
{
    public function toResponse(): ResponseInterface {
        return Response::asJson([
            'error_message' => $this->getMessage(),    
        ], 404);
    }
    
}

$application = new HttpApplication();
$application->get('/article/{id}', function() {
    throw new NotFoundException('Article with given id does not exists');
});

// Run the application.
$application->run();
```
 

### Custom error handling middleware

In any case you would like to provide custom error handler, it can be done by simply creating middleware with try/catch
statement inside `process` method. 
The following code returns custom response in case any error occurs in the application:
 
```php
<?php
// Include composer's autoloader.
require_once __DIR__.'/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Igni\Network\Http\Response;
use Igni\Application\HttpApplication;

class CustomErrorHandler implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface 
    {
        try {
            $response = $next->handle($request);
        } catch (Throwable $throwable) {
            $response = Response::asText('Custom error message', $status = 500);
        }
        
        return $response;
    }
}

$application = new HttpApplication();
$application->use(new CustomErrorHandler());

// Run the application.
$application->run();
```

## Testing
Igni is build to be testable and maintainable in fact most of the crucial framework's layers are covered
with reasonable amount of tests.

Testing your code can be simply performed by executing your controller with mocked ServerRequest object, 
consider following example:
```php
<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Igni\Network\Http\Route;
use Igni\Application\Http\Controller;
use Igni\Network\Http\Response;
use Igni\Network\Http\ServerRequest;

class WelcomeUserController implements Controller
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface 
    {
        return Response::asText("Hi {$request->getAttribute('name')}!");
    }
    
    public static function getRoute(): Route 
    {
        return Route::get('hi/{name}');
    }
}

final class WelcomeUserControllerTest extends TestCase
{
    public function testWelcome(): void
    {
        $controller = new WelcomeUserController();
        $response = $controller(new ServerRequest('/hi/Tom'));
        
        self::assertSame('Hi Tom!', (string) $response->getBody());
        self::assertSame(200, $response->getStatusCode());
    }
}
```


## Working with containers

### Using default container
By default Igni is using its own [dependency injection container](https://github.com/igniphp/container), which provides:

- easy to use interface
- autowiring support
- contextual injection
- free of any configuration or complex building process
- small footprint

So if you are fan of small and easy-to-use solutions there are no steps required in order to use it
within your application.

### Using custom container
Igni can work with any dependency injection container that is PSR-11 compatible service. 
In order to use your favourite DI library just pass it as parameter to application's constructor.
If you container requires building process and you would like to use `ServiceProvider` interface,
it is recommended to provide services as you would do this usually with your modules and attach `OnRunListener` 
to any of your modules and build your container in the provided method:

```php
<?php
// Include composer's autoloader.
require_once __DIR__.'/vendor/autoload.php';

use Igni\Application\Application;
use Igni\Application\Providers\ServiceProvider;
use Igni\Application\Listeners\OnRunListener;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SymfonyDependencyInjectionModule implements ServiceProvider, OnRunListener
{
    public function onRun(Application $application): void 
    {
        /** @var ContainerBuilder $container */
        $container = $application->getContainer();
        $container->compile();
    }
    
    /**
    * @param ContainerBuilder $container
    */
    public function provideServices(ContainerInterface $container): void 
    {
        $container->register('mailer', 'Mailer');
    }
}

$containerBuilder = new ContainerBuilder();
$application = new Igni\Application\HttpApplication($containerBuilder);
$application->use(new SymfonyDependencyInjectionModule());

// Run the application.
$application->run();
```

## Igni server based on swoole

```php
<?php
require_once __DIR__.'/vendor/autoload.php';

use Igni\Application\HttpApplication;
use Igni\Network\Http\Response;
use Igni\Network\Server\HttpServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// Setup server
$server = new HttpServer();

// Setup application and routes
$application = new HttpApplication();
$application->get('/hello/{name}', function (ServerRequestInterface $request) : ResponseInterface {
    return Response::asText("Hello {$request->getAttribute('name')}");
});

// Run the server, it should listen on localhost:80
$application->run($server);

```

## External webserver

### Apache

If you are using Apache, make sure mod_rewrite is enabled and use the following .htaccess file:
 ```xml
 <IfModule mod_rewrite.c>
     Options -MultiViews
 
     RewriteEngine On
     #RewriteBase /path/to/app
     RewriteCond %{REQUEST_FILENAME} !-d
     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteRule ^ index.php [QSA,L]
 </IfModule>
 ```

### nginx + php-fpm

If you are using nginx + php-fpm, the following is minimal configuration to get the things done:

```nginx
server {
    server_name domain.tld www.domain.tld;
    root /var/www/project/web;

    location / {
        # try to serve file directly, fallback to front controller
        try_files $uri /index.php$is_args$args;
    }

    # 
    location ~ ^/index\.php(/|$) {
    
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        if (!-f $document_root$fastcgi_script_name) {
            return 404;
        }
            
        # For socket connection
        fastcgi_pass unix:/var/run/php-fpm.sock;
        
        # Uncomment following line to use tcp connection instead socket
        # fastcgi_pass 127.0.0.1:9000;
        
        include fastcgi_params;
        
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;

        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Enable the internal directive to disable URIs like this
        # internal;
    }

    #return 404 for all php files as we do have a front controller
    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/project_error.log;
    access_log /var/log/nginx/project_access.log;
}
```

### PHP built-in webserver

PHP ships with a built-in webserver for development. This server allows you to run Igni without any configuration. 

```php
<?php
require_once __DIR__.'/vendor/autoload.php';

use Igni\Network\Http\Response;
use Igni\Application\HttpApplication;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

// Setup application and routes
$application = new HttpApplication();
$application->get('/hello/{name}', function (ServerRequestInterface $request): ResponseInterface {
    return Response::asText("Hello {$request->getAttribute('name')}");
});

// Run with the server
$application->run();

```

Assuming your front controller is at ./index.php, you can start the server using the following command:

```
php -S localhost:8080 index.php 
```

 > Note: This should be used only for development.
