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
 * The Collection of services. Provides services on demand.
 */
class Services extends ServiceLocator implements ServicesInterface
{
    /**
     * Merges with other services.
     *
     * @param ServicesInterface $source The data source
     *
     * @return self
     */
    public function merge(ServicesInterface $source)
    {
        $this->registry  = array_merge($this->registry, $source->getRegistry());
        $this->instances = array_merge($this->instances, $source->getInstances());

        return $this;
    }
}
