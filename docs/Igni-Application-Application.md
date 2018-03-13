Igni\Application\Application
===============

Main glue between all components.




* Class name: Application
* Namespace: Igni\Application
* This is an **abstract** class





Properties
----------


### $serviceLocator

    protected \Igni\Container\ServiceLocator $serviceLocator





* Visibility: **protected**


### $config

    protected \Igni\Application\Config $config





* Visibility: **protected**


### $initialized

    private boolean $initialized = false





* Visibility: **private**


### $modules

    protected array<mixed,object> $modules





* Visibility: **protected**


### $dependencyResolver

    protected \Igni\Container\DependencyResolver $dependencyResolver





* Visibility: **protected**


Methods
-------


### __construct

    mixed Igni\Application\Application::__construct(\Psr\Container\ContainerInterface|null $container, \Igni\Application\Config|null $config)

Application constructor.



* Visibility: **public**


#### Arguments
* $container **Psr\Container\ContainerInterface|null**
* $config **Igni\Application\Config|null**



### extend

    mixed Igni\Application\Application::extend($module)

Allows for application extension by modules.

Module can be any valid object or class name.

* Visibility: **public**


#### Arguments
* $module **mixed**



### run

    mixed Igni\Application\Application::run()

Starts the application.

Initialize modules. Performs tasks to generate response for the client.

* Visibility: **public**
* This method is **abstract**.




### getControllerAggregate

    \Igni\Application\Controller\ControllerAggregate Igni\Application\Application::getControllerAggregate()





* Visibility: **public**
* This method is **abstract**.




### getConfig

    \Igni\Application\Config Igni\Application\Application::getConfig()





* Visibility: **public**




### fromIni

    \Igni\Application\Application Igni\Application\Application::fromIni(string $path)

Factory method, for instantiating application from ini file.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $path **string**



### handleOnBootListeners

    mixed Igni\Application\Application::handleOnBootListeners()





* Visibility: **protected**




### handleOnShutDownListeners

    mixed Igni\Application\Application::handleOnShutDownListeners()





* Visibility: **protected**




### handleOnErrorListeners

    mixed Igni\Application\Application::handleOnErrorListeners(\Throwable $exception)





* Visibility: **protected**


#### Arguments
* $exception **Throwable**



### handleOnRunListeners

    mixed Igni\Application\Application::handleOnRunListeners()





* Visibility: **protected**




### initialize

    mixed Igni\Application\Application::initialize()





* Visibility: **protected**




### initializeModule

    mixed Igni\Application\Application::initializeModule($module)





* Visibility: **protected**


#### Arguments
* $module **mixed**


