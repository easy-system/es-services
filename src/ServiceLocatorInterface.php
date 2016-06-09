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

/**
 * The interface for abstract Service Locator.
 */
interface ServiceLocatorInterface extends \Serializable
{
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
    public function add(array $specifications);

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
    public function set($name, $service);

    /**
     * Returns true if the given service is defined.
     *
     * @param string $name The service name
     *
     * @return bool
     */
    public function has($name);

    /**
     * Gets the service.
     *
     * @param string $name The service name
     *
     * @throws \InvalidArgumentException When trying to get an unknown service
     * @throws \RunimeException          If failled build a service
     *
     * @return mixed The requested service
     */
    public function get($name);

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
     * @return mixed New object
     */
    public function build($classNameOrSpecification, $serviceName = '');

    /**
     * Gets the registry of services.
     *
     * @return array The registry of services
     */
    public function getRegistry();

    /**
     * Gets the instances of the constructed services.
     *
     * @return array The instances of of the constructed services
     */
    public function getInstances();
}
