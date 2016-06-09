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

class ServicesTraitTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once 'ServicesTraitTemplate.php';
    }

    public function testGetServices()
    {
        $template = new ServicesTraitTemplate();
        $services = new Services();
        Provider::setServices($services);
        $this->assertSame($services, $template->getServices());
    }

    public function testSetServices()
    {
        $template = new ServicesTraitTemplate();
        $services = new Services();
        $template->setServices($services);
        $this->assertSame($services, $template->getServices());
    }
}
