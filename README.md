# ![Igni logo](https://github.com/igniphp/common/blob/master/logo/full.svg)![Build Status](https://travis-ci.org/igniphp/storage.svg?branch=master)

## Igni framework
Licensed under MIT License.

## Overview

### Introduction
**Igni** framework allows you to write extensible and middleware based REST applications which are [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant.

Igni aims to be:
 - Lightweight: Igni's source code is around 60KB.
 - Extensible: Igni offers extension system that allows to use `PSR-15` middleware (with `zend-stratigility`) and modular architecture.
 - Testable: Igni extends `zend-diactoros` (`PSR-7` implementation) and allows to manually dispatch routes to perform end-to-end tests.
 - Easy to use: Igni exposes an intuitive and concise API. All you need to do to use it is to include composer autoloader.
 
 
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

// Setup application
$application = new Igni\Http\Application();

// Define routing
$application->get('/hello/{name}', function (\Psr\Http\Message\ServerRequestInterface $request) : \Psr\Http\Message\ResponseInterface {
    return \Igni\Http\Response::fromText("Hello {$request->getAttribute('name')}");
});

// Run the application
$application->run();
```
First we include composer's autoloader. 
Instantiation of application happens next than simply controller is attached to the `GET /hello/{name}` request pattern.
And application is run.

Similar approach is taken when build-in server comes in place:

```php
<?php
require_once __DIR__.'/vendor/autoload.php';

// Setup application and routes
$application = new Igni\Http\Application();
$application->get('/hello/{name}', function (\Psr\Http\Message\ServerRequestInterface $request) : \Psr\Http\Message\ResponseInterface {
    return \Igni\Http\Response::fromText("Hello {$request->getAttribute('name')}");
});

// Run with the server
$application->run(new Igni\Http\Server());
```

Server instance is created and passed to application's `run` method.
While using default settings it listens on incoming localhost connections at port `8080`.

## Routing

In Igni you define a route and the controller that is called when that route is matched against server request.

The controller can be any callable or class that implements `\Igni\Http\Controller` interface. 
The return value of callable must implements `\Psr\Http\Message\ResponseInterface`.

The route is a pattern representation of expected URI requested by clients.
Route pattern can use a syntax where `{var}` specifies a placeholder with name `var` and
it matches a regex `[^/]+.`. 

You can specify a custom pattern by writing `{name:pattern}`. Here are some examples:  

```php
<?php

// Matches following get requests: users/42, users/1, but not /users/me 
$application->get('/users/{id:\d+}', $controller);

// Matches following get requests: /users/42, /users/me but not /users/me/bar
$application->get('/users/{name}', $controller);

// Matches following get requests: /users/42, /users/me, /users/me/bar as well
$application->get('/users/{name:.+}', $controller);

```

Custom patterns cannot use capturing groups, for example `/{lang:(en|de)}` is not a valid placeholder instead you can
use `/{lang:en|de}` ([source](https://github.com/nikic/FastRoute/README.md)).

To create optional route parts, the optional part has to be enclosed in `[...]`.

```php
<?php

// Matches: /users, /users/bob, /users/bob/details
$application->get('/users/[{name}[/details]]', $controller);
```

    Please note: Optional parts are only supported in a trailing position.
    
## API reference
Api that is exposed by `Igni\Http\Application`.

### Routing
```php
<?php
// Include composer's autoloader.
require_once __DIR__.'/vendor/autoload.php';

$application = new Igni\Http\Application();
$application->get('/hello/{name}', function ($request) {
    return Igni\Http\Response::fromText("Hello: {$request->getAttribute('name')}");
});
$application->run();
```

#### `Application::get(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `GET` request. 

Should be used to **read** or **retrieve** resource. On success response should return `200` (OK) status code. 
In case of error most often `404` (Not found) or `400` (Bad request) should be returned in the status code.

#### `Application::post(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `POST` request.

Should be used to **create** new resources (can be also used as a wild card verb for operations that don't fit elsewhere). 
On successful creation, response should return `201` (created) or `202` (accepted) status code.
In case of error most often `406` (not acceptable), `409` (conflict) `413` (request entity too large)

#### `Application::put(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `PUT` request.

Should be used to **update** a specific resource (by an identifier) or a collection of resources. 
Can also be used to create a specific resource if the resource identifier is known before-hand.
Response code scenario is same as `post` method with additional `404` if resource to update was not found.

#### `Application::patch(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `PATCH` request.

As `patch` should be used for **modify** resource. The difference is that `patch` request 
can contain only the changes to the resource, not the complete resource as `put` or `post`.

#### `Application::delete(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `DELETE` request.

Should be used to **delete** resource.

#### `Application::options(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `OPTIONS` request.

#### `Application::head(string $route, callable $controller)`

Makes `$controller` to listen on `$route` pattern on http `HEAD` request.

### Middleware

Middleware is an individual component participating in processing incoming request and the creation
of resulting response.

Middleware can be used to:
 - Handle authentication details
 - Perform content negotiation
 - Error handling

In Igni middleware can be any closure that accepts `\Psr\Http\Message\ServerRequestInterface` and `\Psr\Http\Server\RequestHandlerInterface` as parameters 
and returns valid instance of `\Psr\Http\Message\ResponseInterface` or any class/object that implements `\Psr\Http\Server\MiddlewareInterface` interface.

The following code is simple example of middleware that adds custom header to all responses: 

```php
<?php
// Include composer's autoloader.
require_once __DIR__.'/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class BenchmarkMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface 
    {
        $time = microtime();
        $response = $next->handle($request);
        $renderTime = microtime() - $time;
        
        return $response->withHeader('render-time', $renderTime);
    }
}

$application = new Igni\Http\Application();

// Attach custom middleware instance.
$application->use(new BenchmarkMiddleware());

// Attach closure middleware.
$application->use(function(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface {
    $response = $next->handle($request);
    return $response->withHeader('foo', 'bar');
});

// Run the application.
$application->run();
```

