Usage 
=====

# Introduction

The Service Locator design pattern implemented by a class `Es\Services\Services`.

# Obtaining the Services

## Obtaining the services using `ServicesTrait`

The trait `Es\Services\ServicesTrait` contains method `getServices()` for 
retrive the instance of `Es\Services\Services`:

```
use Es\Services\ServicesTrait;

class Example
{
    use ServicesTrait;

    public function foo()
    {
        $services = $this->getServices();
        // ...
    }
}
```

## Obtaining the `Services` from static factory methods
The central point of access to services is the class `Es\Services\Provider`.
It have the static method `getServices()` to retrieve the instance of Services:

```
use Es\Services\Provider;

class ExampleFactory
{
    public static function make()
    {
        $services = Provider::getServices();
        // ...
    }
}
```

# Defining Service

## Defining service class

To define the Service use the following syntax:
```
$services = $this->getServices();
$services->set('Foo.Bar.Baz', 'Foo\Bar\Baz');
```

An instance of `Foo\Bar\Baz` would be built on demand:
```
$services = $this->getServices();
$baz      = $services->get('Foo.Bar.Baz'); 
```

## Defining service factory

To define the factory of Service use the following syntax:
```
$services = $this->getServices();
$services->set('Foo.Bar.Baz', 'Foo\Bar\BazFactory::make');
```
The method "make" of `Foo\Bar\BazFactory`  factory must meet the following 
conditions:

1. It should be static
2. It should return an instance of the service class

An instance of service would be built on demand:
```
$services = $this->getServices();
$baz      = $services->get('Foo.Bar.Baz'); 
```

## Defining absract factory

To define abstract factory use the following syntax:
```
$services = $this->getServices();
$services->set('Foo.Bar.Baz', 'Foo\Bar\BazAbstractFactory::make::');
```
The method "make" of `Foo\Bar\BazAbstractFactory` factory must meet the following 
conditions:

1. It should be static
2. It should return an instance of the service class
3. It must take the service name as an argument.

As example:
```
namespace Foo\Bar;

class BazAbstractFactory
{
    public static function make($name)
    {
        $class    = str_replace('.', '\\', $name);
        $instance = new $class();

        return $instance;
    }
}
```
The `$name` in this case is `Foo.Bar.Baz`.
An instance of service would be built on demand:
```
$services = $this->getServices();
$baz      = $services->get('Foo.Bar.Baz'); 
```

## Defining the specified instance

To define the specified instance use the following syntax:
```
$instance = new \Foo\Bar\Baz();
$services = $this->getServices();
$services->set('Foo.Bar.Baz', $instance);
```

> Note: It is possible to define not only the object but also a scalar variable or array

## Defining the set of serivces

To define abstract factory use the following syntax:
```
$config = [
    'Foo.Bar.Baz' => 'Foo\Bar\Baz',
    'Bon.Con.Com' => 'Bon\Con\Com',
    // ...
];
$services = $this->getServices();
$services->add($config);
```

> Note: Unable to define a instance of class in such a way

# Remove service

To remove the service, set it to null:
```
$services = $this->getServices();
$services->set('Foo.Bar.Baz', null);
```
