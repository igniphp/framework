Igni\Http\Controller
===============

PSR-7 and SRP compatible interface for http controller.

Each controller must handle only one type of request defined by route.


* Interface name: Controller
* Namespace: Igni\Http
* This is an **interface**
* This interface extends: [Igni\Application\Controller](Igni-Application-Controller.md)





Methods
-------


### __invoke

    \Psr\Http\Message\ResponseInterface|\Serializable Igni\Http\Controller::__invoke(\Psr\Http\Message\ServerRequestInterface $request)

Controller should accept server request and return valid response interface.



* Visibility: **public**


#### Arguments
* $request **Psr\Http\Message\ServerRequestInterface**



### getRoute

    \Igni\Http\Route Igni\Http\Controller::getRoute()

Returns the route pattern that will be handled by controller.



* Visibility: **public**
* This method is **static**.



