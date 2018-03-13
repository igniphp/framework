Igni\Http\MiddlewareProvider
===============

Allows modules to provide additional psr-15 compatible middleware.




* Interface name: MiddlewareProvider
* Namespace: Igni\Http
* This is an **interface**






Methods
-------


### provideMiddleware

    mixed Igni\Http\MiddlewareProvider::provideMiddleware(\Igni\Http\MiddlewareAggregate $aggregate)

Registers new middleware in the application scope.



* Visibility: **public**


#### Arguments
* $aggregate **Igni\Http\MiddlewareAggregate**


