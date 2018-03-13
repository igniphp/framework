Igni\Http\Server\ClientStats
===============

Value class for aggregating client statistics.




* Class name: ClientStats
* Namespace: Igni\Http\Server





Properties
----------


### $stats

    private mixed $stats





* Visibility: **private**


Methods
-------


### __construct

    mixed Igni\Http\Server\ClientStats::__construct(array $stats)

ClientStats constructor.



* Visibility: **public**


#### Arguments
* $stats **array**



### getPort

    integer Igni\Http\Server\ClientStats::getPort()

Returns port used by client to connect to the server.



* Visibility: **public**




### getIp

    string Igni\Http\Server\ClientStats::getIp()

Returns client's ip.



* Visibility: **public**




### getConnectTime

    integer Igni\Http\Server\ClientStats::getConnectTime()

Returns unix timestamp when connection happened.



* Visibility: **public**



