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
 * Standard interaction with the service provider.
 */
trait ServicesTrait
{
    /**
     * Sets the Services.
     *
     * @param ServicesInterface $services The Collection of services
     */
    public function setServices(ServicesInterface $services)
    {
        Provider::setServices($services);
    }

    /**
     * Gets the Services.
     *
     * @return ServicesInterface The Collection of services
     */
    public function getServices()
    {
        return Provider::getServices();
    }
}
