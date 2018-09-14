## Release notes for 2.x

### 2.0.0

#### Fixed
- Server listeners now are refactored to use own pipes, this fixes issue where some listeners were overriding others

#### Changed
- Error handling improvements:
    - `Igni\Network\Exception\HttpException` interface now provides one `toResponse` method instead `getHttpCode` and `getHttpBody`
    - `Igni\Application\Listeners\OnErrorListener` allows to swap the exception
- Removed not found middleware, now error middleware keeps track of not found routes as well
- `Igni\Http\Application` becomes `Igni\Application\HttpApplication`
- `Igni\Http\Application` becomes `Igni\Application\HttpApplication`
- `Igni\Application\Controller\ControllerAggregate` interface becomes `Igni\Application\ControllerAggregator`
- `Igni\Application\Controller\ControllerAggregate::add` method was rename to `Igni\Application\ControllerAggregator::register`
- `Igni\Http\Controller\ControllerAggregate` gets removed and responsibility is passed to `Igni\Application\HttpApplication` 
- `Igni\Http\MiddlewareProvider` becomes  `Igni\Application\Providers\MiddlewareProvider`
- `Igni\Http\Server` becomes `Igni\Network\Server\HttpServer`
- `Igni\Http\Server\HttpConfiguration` becomes `Igni\Network\Server\Configuration`
- `Igni\Http\Response` becomes `Igni\Network\Http\Response`
- `Igni\Http\Route` becomes `Igni\Network\Http\Route`
- `Igni\Http\Router` becomes `Igni\Application\Http\GenericRouter`
- `Igni\Http\Route::from*` methods were rename to `Igni\Network\Http\Route::as*` methods
