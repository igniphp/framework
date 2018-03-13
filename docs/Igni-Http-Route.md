Igni\Http\Route
===============

Represents route pattern.




* Class name: Route
* Namespace: Igni\Http





Properties
----------


### $expression

    private string $expression





* Visibility: **private**


### $method

    private string $method





* Visibility: **private**


### $attributes

    private array $attributes = array()





* Visibility: **private**


### $handler

    private callable $handler

Callable or valid class name



* Visibility: **private**


Methods
-------


### __construct

    mixed Igni\Http\Route::__construct(string $expression, string $method)

Route constructor.



* Visibility: **public**


#### Arguments
* $expression **string**
* $method **string**



### delegate

    \Igni\Http\Route Igni\Http\Route::delegate($handler)

Sets controller that is going to handle all request
which uri matches the route pattern.



* Visibility: **public**


#### Arguments
* $handler **mixed**



### getExpression

    string Igni\Http\Route::getExpression()

Returns route pattern.



* Visibility: **public**




### getMethod

    string Igni\Http\Route::getMethod()

Returns request method.



* Visibility: **public**




### getDelegator

    callable|string Igni\Http\Route::getDelegator()

Returns controller that listen to request
which uri matches the route pattern.



* Visibility: **public**




### withAttributes

    \Igni\Http\Route Igni\Http\Route::withAttributes(array $attributes)

Factories new instance of the current route with
attributes retrieved from client's request.



* Visibility: **public**


#### Arguments
* $attributes **array**



### getAttributes

    array Igni\Http\Route::getAttributes()

Returns attributes extracted from the uri.



* Visibility: **public**




### get

    \Igni\Http\Route Igni\Http\Route::get(string $expression)

Factories new instance of the route
that will be matched against get request.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $expression **string**



### post

    \Igni\Http\Route Igni\Http\Route::post(string $expression)

Factories new instance of the route
that will be matched against post request.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $expression **string**



### put

    \Igni\Http\Route Igni\Http\Route::put(string $expression)

Factories new instance of the route
that will be matched against put request.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $expression **string**



### delete

    \Igni\Http\Route Igni\Http\Route::delete(string $expression)

Factories new instance of the route
that will be matched against delete request.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $expression **string**



### patch

    \Igni\Http\Route Igni\Http\Route::patch(string $expression)

Factories new instance of the route
that will be matched against patch request.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $expression **string**



### head

    \Igni\Http\Route Igni\Http\Route::head(string $expression)

Factories new instance of the route
that will be matched against head request.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $expression **string**



### options

    \Igni\Http\Route Igni\Http\Route::options(string $expression)

Factories new instance of the route
that will be matched against options request.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $expression **string**


