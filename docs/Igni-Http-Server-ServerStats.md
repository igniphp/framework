Igni\Http\Server\ServerStats
===============

Value class that aggregates server&#039;s statistics.




* Class name: ServerStats
* Namespace: Igni\Http\Server





Properties
----------


### $stats

    private mixed $stats





* Visibility: **private**


Methods
-------


### __construct

    mixed Igni\Http\Server\ServerStats::__construct(array $stats)

ServerStats constructor.



* Visibility: **public**


#### Arguments
* $stats **array**



### getStartTime

    integer Igni\Http\Server\ServerStats::getStartTime()

Returns the timestamp of server since start



* Visibility: **public**




### getConnections

    integer Igni\Http\Server\ServerStats::getConnections()

Returns the number of current connections



* Visibility: **public**




### getAcceptedConnections

    integer Igni\Http\Server\ServerStats::getAcceptedConnections()

Returns the number of accepted connections



* Visibility: **public**




### getClosedConnections

    integer Igni\Http\Server\ServerStats::getClosedConnections()

Returns the number of closed connections



* Visibility: **public**




### getAwaitingConnections

    integer Igni\Http\Server\ServerStats::getAwaitingConnections()

Returns the number of queuing up tasks



* Visibility: **public**




### getReceivedRequests

    integer Igni\Http\Server\ServerStats::getReceivedRequests()

Returns the number of received requests



* Visibility: **public**



