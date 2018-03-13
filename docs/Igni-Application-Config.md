Igni\Application\Config
===============

Application&#039;s config container.

Treats dots as an operator for accessing nested values.
If constant name is put in curly braces as a value, it wil be replaced
to the constant value.


* Class name: Config
* Namespace: Igni\Application





Properties
----------


### $prototypes

    private mixed $prototypes = array('http' => array('class' => \Igni\Http\Application::class, 'type' => 'http', 'container' => \Igni\Container\ServiceLocator::class), 'mysql' => array('host' => 'localhost', 'port' => 3306, 'type' => 'mysql', 'username' => '', 'password' => '', 'errors' => true, 'persistence' => true), 'sqlite' => array('type' => 'sqlite', 'errors' => true), 'mongo' => array('host' => 'localhost', 'port' => 27017, 'type' => 'mongodb', 'username' => '', 'password' => '', 'errors' => true, 'readConcern' => 'majority', 'writeConcern' => 'majority'), 'pgsql' => array('host' => 'localhost', 'port' => 5432, 'type' => 'pgsql', 'username' => '', 'password' => '', 'errors' => true, 'persistence' => true))





* Visibility: **private**
* This property is **static**.


### $config

    private array $config





* Visibility: **private**


Methods
-------


### __construct

    mixed Igni\Application\Config::__construct(array $config)

Config constructor.



* Visibility: **public**


#### Arguments
* $config **array**



### has

    boolean Igni\Application\Config::has(string $key)

Checks if config key exists.



* Visibility: **public**


#### Arguments
* $key **string**



### get

    null|string|array<mixed,string> Igni\Application\Config::get(string $key, null $default)

Gets value behind the key, or returns $default value if path does not exists.



* Visibility: **public**


#### Arguments
* $key **string**
* $default **null**



### merge

    \Igni\Application\Config Igni\Application\Config::merge(\Igni\Application\Config $config)

Merges one instance of Config class into current one and
returns current instance.



* Visibility: **public**


#### Arguments
* $config **[Igni\Application\Config](Igni-Application-Config.md)**



### set

    mixed Igni\Application\Config::set(string $key, $value)

Sets new value.



* Visibility: **public**


#### Arguments
* $key **string**
* $value **mixed**



### toArray

    array Igni\Application\Config::toArray()

Returns array representation of the config.



* Visibility: **public**




### fromIni

    \Igni\Application\Config Igni\Application\Config::fromIni(string $path)

Factories config class from ini file.

Supports config autoload by glob search.

* Visibility: **public**
* This method is **static**.


#### Arguments
* $path **string**



### fetchConstants

    mixed Igni\Application\Config::fetchConstants($value)





* Visibility: **private**


#### Arguments
* $value **mixed**


