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
 * The interface for the Collection of services.
 */
interface ServicesInterface extends ServiceLocatorInterface
{
    /**
     * Merges with other services.
     *
     * @param ServicesInterface $source The data source
     *
     * @return self
     */
    public function merge(ServicesInterface $source);
}
