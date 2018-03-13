Igni\Http\Router
===============

Utility class for simplifying api for nikic router.




* Class name: Router
* Namespace: Igni\Http





Properties
----------


### $routeParser

    private \FastRoute\RouteParser $routeParser





* Visibility: **private**


### $dataGenerator

    private \FastRoute\DataGenerator $dataGenerator





* Visibility: **private**


Methods
-------


### __construct

    mixed Igni\Http\Router::__construct(\FastRoute\RouteParser $routeParser, \FastRoute\DataGenerator $dataGenerator)

Router constructor.



* Visibility: **public**


#### Arguments
* $routeParser **FastRoute\RouteParser**
* $dataGenerator **FastRoute\DataGenerator**



### addRoute

    mixed Igni\Http\Router::addRoute(\Igni\Http\Route $route)

Registers new route.



* Visibility: **public**


#### Arguments
* $route **[Igni\Http\Route](Igni-Http-Route.md)**



### findRoute

    \Igni\Http\Route Igni\Http\Router::findRoute(string $method, string $uri)

Finds route matching clients request.



* Visibility: **public**


#### Arguments
* $method **string** - &lt;p&gt;request method.&lt;/p&gt;
* $uri **string** - &lt;p&gt;request uri.&lt;/p&gt;



### getData

    mixed Igni\Http\Router::getData()





* Visibility: **protected**



