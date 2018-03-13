Igni\Http\Server\OnShutdown
===============

The event happens when the server shuts down

Before the shutdown happens all the client connections are closed.


* Interface name: OnShutdown
* Namespace: Igni\Http\Server
* This is an **interface**
* This interface extends: [Igni\Http\Server\Listener](Igni-Http-Server-Listener.md)





Methods
-------


### onShutdown

    mixed Igni\Http\Server\OnShutdown::onShutdown(\Igni\Http\Server $server)

Handles server's shutdown event.



* Visibility: **public**


#### Arguments
* $server **Igni\Http\Server**


