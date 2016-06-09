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
 * Provides the Collection of services.
 * This is the central point of access to services of the system.
 * You can access it directly from the static factory method.
 * For non-static methods, use the Es\Services\ServicesTrait.
 */
class Provider
{
    /**
     * The services.
     *
     * @var ServicesInterface
     */
    protected static $services;

    /**
     * Gets services.
     *
     * @return ServicesInterface The collection of services
     */
    public static function getServices()
    {
        if (! static::$services) {
            static::$services = new Services();
        }

        return static::$services;
    }

    /**
     * Sets services.
     *
     * @param ServicesInterface $services The collection of services
     */
    public static function setServices(ServicesInterface $services)
    {
        static::$services = $services;
    }
}
