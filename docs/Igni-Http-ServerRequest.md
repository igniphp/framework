Igni\Http\ServerRequest
===============






* Class name: ServerRequest
* Namespace: Igni\Http
* Parent class: Igni\Http\Request
* This class implements: Psr\Http\Message\ServerRequestInterface




Properties
----------


### $attributes

    private array $attributes = array()





* Visibility: **private**


### $cookieParams

    private array $cookieParams = array()





* Visibility: **private**


### $parsedBody

    private null $parsedBody





* Visibility: **private**


### $queryParams

    private array $queryParams = array()





* Visibility: **private**


### $serverParams

    private array $serverParams





* Visibility: **private**


### $uploadedFiles

    private array $uploadedFiles





* Visibility: **private**


Methods
-------


### __construct

    mixed Igni\Http\ServerRequest::__construct(array $serverParams, array $uploadedFiles, null|string $uri, null|string $method, string|resource|\Psr\Http\Message\StreamInterface $body, array $headers)

Server request constructor.



* Visibility: **public**


#### Arguments
* $serverParams **array** - &lt;p&gt;Server parameters, typically from $_SERVER&lt;/p&gt;
* $uploadedFiles **array** - &lt;p&gt;Upload file information, a tree of UploadedFiles&lt;/p&gt;
* $uri **null|string** - &lt;p&gt;URI for the request, if any.&lt;/p&gt;
* $method **null|string** - &lt;p&gt;HTTP method for the request, if any.&lt;/p&gt;
* $body **string|resource|Psr\Http\Message\StreamInterface** - &lt;p&gt;Messages body, if any.&lt;/p&gt;
* $headers **array** - &lt;p&gt;Headers for the message, if any.&lt;/p&gt;



### getServerParams

    mixed Igni\Http\ServerRequest::getServerParams()

{@inheritdoc}



* Visibility: **public**




### getUploadedFiles

    mixed Igni\Http\ServerRequest::getUploadedFiles()

{@inheritdoc}



* Visibility: **public**




### withUploadedFiles

    mixed Igni\Http\ServerRequest::withUploadedFiles(array $uploadedFiles)

{@inheritdoc}



* Visibility: **public**


#### Arguments
* $uploadedFiles **array**



### getCookieParams

    mixed Igni\Http\ServerRequest::getCookieParams()

{@inheritdoc}



* Visibility: **public**




### withCookieParams

    mixed Igni\Http\ServerRequest::withCookieParams(array $cookies)

{@inheritdoc}



* Visibility: **public**


#### Arguments
* $cookies **array**



### getQueryParams

    mixed Igni\Http\ServerRequest::getQueryParams()

{@inheritdoc}



* Visibility: **public**




### withQueryParams

    mixed Igni\Http\ServerRequest::withQueryParams(array $query)

{@inheritdoc}



* Visibility: **public**


#### Arguments
* $query **array**



### getParsedBody

    mixed Igni\Http\ServerRequest::getParsedBody()

{@inheritdoc}



* Visibility: **public**




### withParsedBody

    mixed Igni\Http\ServerRequest::withParsedBody($data)

{@inheritdoc}



* Visibility: **public**


#### Arguments
* $data **mixed**



### getAttributes

    mixed Igni\Http\ServerRequest::getAttributes()

{@inheritdoc}



* Visibility: **public**




### getAttribute

    mixed Igni\Http\ServerRequest::getAttribute($attribute, $default)

{@inheritdoc}



* Visibility: **public**


#### Arguments
* $attribute **mixed**
* $default **mixed**



### withAttribute

    mixed Igni\Http\ServerRequest::withAttribute($attribute, $value)

{@inheritdoc}



* Visibility: **public**


#### Arguments
* $attribute **mixed**
* $value **mixed**



### withoutAttribute

    mixed Igni\Http\ServerRequest::withoutAttribute($attribute)

{@inheritdoc}



* Visibility: **public**


#### Arguments
* $attribute **mixed**



### withAttributes

    \Igni\Http\ServerRequest Igni\Http\ServerRequest::withAttributes(array $attributes)

Sets request attributes

This method returns a new instance.

* Visibility: **public**


#### Arguments
* $attributes **array**



### validateUploadedFiles

    mixed Igni\Http\ServerRequest::validateUploadedFiles(array $uploadedFiles)

Recursively validate the structure in an uploaded files array.



* Visibility: **private**


#### Arguments
* $uploadedFiles **array**



### fromGlobals

    mixed Igni\Http\ServerRequest::fromGlobals()





* Visibility: **public**
* This method is **static**.




### fromSwooleRequest

    \Igni\Http\ServerRequest Igni\Http\ServerRequest::fromSwooleRequest(\Swoole\Http\Request $request)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $request **Swoole\Http\Request**



### fromUri

    mixed Igni\Http\ServerRequest::fromUri($uri, $method, \Igni\Http\string $body)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $uri **mixed**
* $method **mixed**
* $body **Igni\Http\string**



### fromPsrServerRequest

    mixed Igni\Http\ServerRequest::fromPsrServerRequest(\Psr\Http\Message\ServerRequestInterface $psrRequest)





* Visibility: **private**
* This method is **static**.


#### Arguments
* $psrRequest **Psr\Http\Message\ServerRequestInterface**


