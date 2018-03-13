Igni\Http\Server\OnRequest
===============

The event happens when the worker process receives the request data.




* Interface name: OnRequest
* Namespace: Igni\Http\Server
* This is an **interface**
* This interface extends: [Igni\Http\Server\Listener](Igni-Http-Server-Listener.md)





Methods
-------


### onRequest

    \Psr\Http\Message\ResponseInterface Igni\Http\Server\OnRequest::onRequest(\Psr\Http\Message\ServerRequestInterface $request)

Handles client request.



* Visibility: **public**


#### Arguments
* $request **Psr\Http\Message\ServerRequestInterface**


