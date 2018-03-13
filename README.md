# ![Igni logo](https://github.com/igniphp/common/blob/master/logo/full.svg)![Build Status](https://travis-ci.org/igniphp/storage.svg?branch=master)

## Igni framework
Licensed under MIT License.

## Table of Contents
 * [Overview](#overview)
    + [Introduction](#introduction)
    + [Api reference](#api-reference)
    + [Installation](#installation)
    + [Usage](#usage)
  * [Routing](#routing)
  * [Middleware](#middleware)
  * [Modules](#modules)
    + [Listeners](#listeners)
    + [Providers](#providers)
  * [The Request](#the-request)
  * [The Response](#the-response)
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
**Igni** framework allows you to write extensible and middleware based REST applications which are [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant.

Igni aims to be:
 - Lightweight: Igni's source code is around 60KB.
 - Extensible: Igni offers extension system that allows to use `PSR-15` middleware (with `zend-stratigility`) and modular architecture.
 - Testable: Igni extends `zend-diactoros` (`PSR-7` implementation) and allows to manually dispatch routes to perform end-to-end tests.
 - Easy to use: Igni exposes an intuitive and concise API. All you need to do to use it is to include composer autoloader.
 
### Api reference
 
Api reference can be found [here](./docs/ApiIndex.md)

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

 > Please note: Optional parts are only supported in a trailing position.
    
Full example:
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

## Middleware

Middleware is an individual component participating in processing incoming request and the creation
of resulting response.

Middleware can be used to:
 - Handle authentication details
 - Perform content negotiation
 - Error handling

In Igni middleware can be any closure that accepts `\Psr\Http\Message\ServerRequestInterface` and `\Psr\Http\Server\RequestHandlerInterface` as parameters 
and returns valid instance of `\Psr\Http\Message\ResponseInterface` or any class/object that implements `\Psr\Http\Server\MiddlewareInterface` interface.

You can add as many middleware as you want, and they are triggered in the same order as you add them. 
In fact even `Igni\Http\Application` is a middleware itself which is automatically added at the end of the pipe.

#### Example
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

Example:
```php
<?php
require_once __DIR__.'/vendor/autoload.php';

use Igni\Application\Controller\ControllerAggregate;
use Igni\Application\Providers\ControllerProvider;
use Igni\Http\Application;
use Igni\Http\Response;
use Igni\Http\Route;

/**
 * Module definition.
 */
class SimpleModule implements ControllerProvider
{
    public function provideControllers(ControllerAggregate $controllers): void
    {
        // Add controller that greets client when /hello/{name} URI is requested
        $controllers->add(function ($request) {
            return Response::fromText("Hello {$request->getAttribute('name')}!");
        }, Route::get('/hello/{name}'));
    }
}

$application = new Application();

// Extend application with the module.
$application->extend(\SimpleModule::class);

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

## The Request
Igni's controllers and middleware are given a PSR-7 server request object that represents http request send 
by the client. Request contains route's params, body, request method, request uri and so on.

For information how to work with PSR-7 [read this](https://www.php-fig.org/psr/psr-7/). 

## The Response
Igni's controllers and middleware must return valid PSR-7 response object. 
Igni's `Igni\Http\Response` class provides factories methods to simplify response creation.

#### `Response::empty(int $status = 200, array $headers = [])`

Creates empty PSR-7 response object.

#### `Response::fromText(string $text, int $status = 200, array $headers = [])`

Creates PSR-7 request with content type set to `text/plain` and body containing passed `$text`

#### `Response::fromJson($data, int $status = 200, array $headers = [])`

Creates PSR-7 request with content type set to `application/json` and body containing json data.
`$data` can be array or `\JsonSerializable` instance.

#### `Response::fromHtml(string $html, int $status = 200, array $headers = [])`

Creates PSR-7 request with content type set to `text/html` and body containing passed html.

#### `Response::fromXml($data, int $status = 200, array $headers = [])`

Creates PSR-7 request with content type set to `application/xml` and body containing xml string.
`$data` can be `\SimpleXMLElement`, `\DOMDocument` or just plain string.

## Igni's server

Igni is shipped with an async non-blocking IO, multiple process HTTP server. 
The server requires `swoole` extension to be installed and enabled, 
more information about swoole can be found [here](https://www.swoole.co.uk).

### Installation

Linux users:

```
pecl install swoole
```

Mac users with homebrew:

```
brew install swoole
```
or:
```
brew install homebrew/php/php71-swoole
```

### Basic Usage

```php
<?php
// Autoloader.
require_once __DIR__.'/vendor/autoload.php';

// Create server instance.
$server = new \Igni\Http\Server();
$server->start();
```

### Listeners

Igni http server uses event-driven model that makes it easy to scale and extend.

There are five type of events available, each of them extends `Igni\Http\Server\Listener` interface:

 - `Igni\Http\Server\OnStart` fired when server starts
 - `Igni\Http\Server\OnStop` fired when server stops
 - `Igni\Http\Server\OnConnect` fired when new client connects to the server
 - `Igni\Http\Server\OnClose` fired when connection with the client is closed
 - `Igni\Http\Server\OnRequest` fired when new request is dispatched
 
 ```php
 <?php
 // Autoloader.
 require_once __DIR__.'/vendor/autoload.php';
 
 // Create server instance.
 $server = new \Igni\Http\Server();
 
 // Each request will retrieve 'Hello' response
 $server->addListener(new class implements \Igni\Http\Server\OnRequest {
     public function onRequest(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface {
        return \Igni\Http\Response::fromText('Hello');
     }
 });
 $server->start();
 ```

### Configuration

Server can be easily configured with `Igni\Http\Server\HttpConfiguration` class.

Please consider following example:
```php
<?php
// Autoloader.
require_once __DIR__.'/vendor/autoload.php';

// Listen on localhost at port 80.
$configuration = new \Igni\Http\Server\HttpConfiguration('0.0.0.0', 80);

// Create server instance.
$server = new \Igni\Http\Server($configuration);
$server->start();
```

##### Enabling ssl support
```php
<?php
// Autoloader.
require_once __DIR__.'/vendor/autoload.php';

$configuration = new \Igni\Http\Server\HttpConfiguration();
$configuration->enableSsl($certFile, $keyFile);

// Create server instance.
$server = new \Igni\Http\Server($configuration);
$server->start();
```

##### Running server as a daemon
```php
<?php
// Autoloader.
require_once __DIR__.'/vendor/autoload.php';

$configuration = new \Igni\Http\Server\HttpConfiguration();
$configuration->enableDaemon($pidFile);

// Create server instance.
$server = new \Igni\Http\Server($configuration);
$server->start();
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

// Setup application and routes
$application = new Igni\Http\Application();
$application->get('/hello/{name}', function (\Psr\Http\Message\ServerRequestInterface $request) : \Psr\Http\Message\ResponseInterface {
    return \Igni\Http\Response::fromText("Hello {$request->getAttribute('name')}");
});

// Run with the server
if (php_sapi_name() == 'cli-server') {
    $application->run();
} else {
    $application->run(new Igni\Http\Server());
}
```

Assuming your front controller is at ./index.php, you can start the server using the following command:

```
php -S localhost:8080 index.php 
```

 > Note: This should be used only for development.
