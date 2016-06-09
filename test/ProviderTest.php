<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Services\Test;

use Es\Services\Provider;
use Es\Services\Services;
use Es\Services\ServicesInterface;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetServices()
    {
        $this->assertInstanceOf(ServicesInterface::CLASS, Provider::getServices());
    }

    public function testSetServices()
    {
        $services = new Services();
        Provider::setServices($services);
        $this->assertSame(Provider::getServices(), $services);
    }
}
