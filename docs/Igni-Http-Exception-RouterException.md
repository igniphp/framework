Igni\Http\Exception\RouterException
===============






* Class name: RouterException
* Namespace: Igni\Http\Exception
* Parent class: Igni\Exception\RuntimeException
* This class implements: [Igni\Http\Exception\HttpExceptionInterface](Igni-Http-Exception-HttpExceptionInterface.md)




Properties
----------


### $httpStatus

    private mixed $httpStatus





* Visibility: **private**


Methods
-------


### noRouteMatchesRequestedUri

    mixed Igni\Http\Exception\RouterException::noRouteMatchesRequestedUri(\Igni\Http\Exception\string $uri)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $uri **Igni\Http\Exception\string**



### methodNotAllowed

    mixed Igni\Http\Exception\RouterException::methodNotAllowed(\Igni\Http\Exception\string $uri, array $allowedMethods)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $uri **Igni\Http\Exception\string**
* $allowedMethods **array**



### invalidRoute

    mixed Igni\Http\Exception\RouterException::invalidRoute($given)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $given **mixed**



### getHttpStatusCode

    mixed Igni\Http\Exception\HttpExceptionInterface::getHttpStatusCode()





* Visibility: **public**
* This method is defined by [Igni\Http\Exception\HttpExceptionInterface](Igni-Http-Exception-HttpExceptionInterface.md)




### getHttpBody

    mixed Igni\Http\Exception\HttpExceptionInterface::getHttpBody()





* Visibility: **public**
* This method is defined by [Igni\Http\Exception\HttpExceptionInterface](Igni-Http-Exception-HttpExceptionInterface.md)



