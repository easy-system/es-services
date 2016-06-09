<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Services;

use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * The abstract Service Locator.
 */
abstract class ServiceLocator implements ServiceLocatorInterface
{
    /**
     * The class of exception, which should be raised if the requested service
     * is not found.
     */
    const NOT_FOUND_EXCEPTION = 'Es\\Services\\Exception\\ServiceNotFoundException';

    /**
     * The message of exception, that thrown when unable to find the requested
     * service.
     *
     * @const string
     */
    const NOT_FOUND_MESSAGE = 'Not found; the Service "%s" is unknown.';

    /**
     * The message of exception, that thrown when unable to build the requested
     * service.
     *
     * @const string
     */
    const BUILD_FAILURE_MESSAGE = 'Failed to create the Service "%s".';

    /**
     * The message of exception, that thrown when added of invalid
     * service specification.
     *
     * @const string
     */
    const INVALID_ARGUMENT_MESSAGE = 'Invalid specification of Service "%s"; expects string, "%s" given.';

    /**
     * The specifications of services.
     *
     * @var array
     */
    protected $registry = [];

    /**
     * The instances of services.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Adds the services.
     * This method will accept only a service specification (the name of
     * class or factory). So, you can not register as a service the already
     * created object.
     * Use this method always when the configuration should be cached.
     *
     * @param array $specifications The array of servises names and their
     *                              specifications
     *
     * @throws \InvalidArgumentException If instead of the service specification
     *                                   will be something else
     *
     * @return self
     */
    public function add(array $specifications)
    {
        foreach ($specifications as $name => $item) {
            if (! is_string($item)) {
                throw new InvalidArgumentException(
                    sprintf(
                        static::INVALID_ARGUMENT_MESSAGE,
                        $name,
                        is_object($item) ? get_class($item) : gettype($item)
                    )
                );
            }
            if (isset($this->instances[$name])) {
                unset($this->instances[$name]);
            }
            $this->registry[$name] = $item;
        }

        return $this;
    }

    /**
     * Sets the service.
     *
     * @param string $name    The service name
     * @param mixed  $service The service specification or instance of a
     *                        service or null. If the service set as null, it
     *                        will be removed.
     *
     * @return self
     */
    public function set($name, $service)
    {
        if (isset($this->registry[$name])) {
            unset($this->registry[$name]);
        }
        if (isset($this->instances[$name])) {
            unset($this->instances[$name]);
        }
        if (null === $service) {
            return $this;
        }
        if (! is_string($service)) {
            $this->instances[$name] = $service;

            return $this;
        }
        $this->registry[$name] = $service;

        return $this;
    }

    /**
     * Returns true if the given service is defined.
     *
     * @param string $name The service name
     *
     * @return bool
     */
    public function has($name)
    {
        if (! isset($this->instances[$name])) {
            return isset($this->registry[$name]);
        }

        return true;
    }

    /**
     * Gets the service.
     *
     * @param string $name The service name
     *
     * @throws Exception\ServiceNotFoundException If the specified service not fount
     * @throws \RunimeException                   If failled build a service
     *
     * @return mixed The requested service
     */
    public function get($name)
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }
        if (! isset($this->registry[$name])) {
            $exceptionClass = static::NOT_FOUND_EXCEPTION;
            throw new $exceptionClass(
                sprintf(static::NOT_FOUND_MESSAGE, $name)
            );
        }
        try {
            $this->instances[$name] = $this->build($this->registry[$name], $name);
        } catch (Exception $e) {
            throw new RuntimeException(
                sprintf(static::BUILD_FAILURE_MESSAGE, $name),
                null,
                $e
            );
        }

        return $this->instances[$name];
    }

    /**
     * Builds an object.
     *
     * @param string $classNameOrSpecification The class of service or the
     *                                         service specification
     * @param string $serviceName              Optional; a abstract factory
     *                                         takes the service name as an
     *                                         argument
     *
     * @throws \RuntimeException If unable build an object
     *
     * @return object New object
     */
    public function build($classNameOrSpecification, $serviceName = '')
    {
        /*
         * Builds uses static factory method
         */
        if (false !== strpos($classNameOrSpecification, '::')) {
            $specification = explode('::', $classNameOrSpecification);
            $className     = $specification[0];
            $factoryMethod = $specification[1];
            if (! class_exists($className, true)) {
                throw new RuntimeException(
                    sprintf(
                        'Factory "%s" not found.',
                        $className
                    )
                );
            }
            if (! is_callable([$className, $factoryMethod])) {
                throw new RuntimeException(
                    sprintf(
                        'Factory method "%s" of class "%s" is not callable.',
                        $factoryMethod,
                        $className
                    )
                );
            }
            if (count($specification) > 2) {
                /*
                 * The special case of factory specification.
                 * Abstract Factory takes the service name as an argument.
                 *
                 * Abstract factory method specification
                 * as 'FactoryClassName::methodName::'
                 */
                $instance = call_user_func(
                    [$className, $factoryMethod],
                    (string) $serviceName
                );
            } else {
                /*
                 * Calls the factory method.
                 *
                 * Factory method specification
                 * as 'FactoryClassName::methodName'
                 */
                $instance = call_user_func([$className, $factoryMethod]);
            }

        /*
         * Builds uses "new" operator
         */
        } else {
            $className = $classNameOrSpecification;
            if (! class_exists($className, true)) {
                throw new RuntimeException(
                    sprintf(
                        'Class "%s" not found.',
                        $className
                    )
                );
            }
            $instance = new $className();
        }

        return $instance;
    }

    /**
     * Gets the registry of services.
     *
     * @return array The registry of services
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * Gets the instances of the constructed services.
     *
     * @return array The instances of of the constructed services
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * Serializes registry of Service Locator.
     *
     * @return string The string representation of object
     */
    public function serialize()
    {
        return serialize($this->registry);
    }

    /**
     * Constructs the Service Locator uses serialized registry.
     *
     * @param  string The string representation of object
     */
    public function unserialize($serialized)
    {
        $this->registry = unserialize($serialized);
    }
}
