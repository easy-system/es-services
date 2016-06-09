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

use Es\Services\Exception\ServiceNotFoundException;
use Es\Services\Services;
use ReflectionProperty;

class ServiceLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see ::testBuildUsingFactoryMethod
     */
    public static function make()
    {
        return new \stdClass();
    }

    /**
     * @see ::testBuildUsingAbstractFactoryMethod
     */
    public static function makeAbstract($serviceName)
    {
        return $serviceName . 'Instance';
    }

    /**
     * @see ::testBuildThrowExceptionIfFactoryMethodIsNotCallable
     */
    private static function privateMake()
    {
        //
    }

    public function testSetStringSetsRegistryItem()
    {
        $locator = new Services();

        $return = $locator->set('foo', 'bar');
        $this->assertSame($return, $locator);

        $registry = $this->extractRegistry($locator);
        $this->assertTrue(isset($registry['foo']));
        $this->assertSame('bar', $registry['foo']);

        $instances = $this->extractInstances($locator);
        $this->assertFalse(isset($instances['foo']));
    }

    public function nonStringItemDataProvider()
    {
        return [
            [true],
            [false],
            [100],
            [['foo']],
            [new \stdClass()],
        ];
    }

    /**
     * @dataProvider nonStringItemDataProvider
     */
    public function testSetNonStringSetsInstancesItem($item)
    {
        $locator = new Services();

        $return = $locator->set('foo', $item);
        $this->assertSame($return, $locator);

        $instances = $this->extractInstances($locator);
        $this->assertTrue(isset($instances['foo']));
        $this->assertSame($item, $instances['foo']);

        $registry = $this->extractRegistry($locator);
        $this->assertFalse(isset($registry['foo']));
    }

    public function testSetNullResetsRegistryItemAndServicesItem()
    {
        $locator = new Services();

        $return = $locator->set('foo', 'stdClass');
        $this->assertSame($return, $locator);

        $instance = $locator->get('foo');

        $registry = $this->extractRegistry($locator);
        $this->assertTrue(isset($registry['foo']));

        $instances = $this->extractInstances($locator);
        $this->assertTrue(isset($instances['foo']));

        $locator->set('foo', null);

        $registry = $this->extractRegistry($locator);
        $this->assertFalse(isset($registry['foo']));

        $instances = $this->extractInstances($locator);
        $this->assertFalse(isset($instances['foo']));
    }

    public function testSetNewRegistryItemResetsInstancesItem()
    {
        $locator = new Services();

        $locator->set('foo', 'stdClass');
        $instance = $locator->get('foo');

        $instances = $this->extractInstances($locator);
        $this->assertTrue(isset($instances['foo']));

        $locator->set('foo', 'bar');

        $instances = $this->extractInstances($locator);
        $this->assertFalse(isset($instances['foo']));
    }

    public function testSetNewInstanceItemResetsRegistryItem()
    {
        $locator = new Services();

        $locator->set('foo', 'stdClass');

        $registry = $this->extractRegistry($locator);
        $this->assertTrue(isset($registry['foo']));

        $locator->set('foo', new \stdClass());

        $registry = $this->extractRegistry($locator);
        $this->assertFalse(isset($registry['foo']));
    }

    public function testAddAddsRegistryItems()
    {
        $config = [
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ];
        $locator = new Services();

        $return = $locator->add($config);
        $this->assertSame($return, $locator);

        $registry = $this->extractRegistry($locator);
        $this->assertSame($config, $registry);
    }

    public function testAddResetsInstanceIfAny()
    {
        $locator = new Services();
        $locator->set('foo', new \stdClass());

        $instances = $this->extractInstances($locator);
        $this->assertTrue(isset($instances['foo']));

        $config = [
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ];
        $locator->add($config);

        $instances = $this->extractInstances($locator);
        $this->assertFalse(isset($instances['foo']));
    }

    public function testAddThrowExceptionIfServiceSpecificationIsNotString()
    {
        $registry = [
            'foo' => 'foo',
            'bar' => new \stdClass(),
            'baz' => 'baz',
        ];
        $locator = new Services();
        $this->setExpectedException('InvalidArgumentException');
        $locator->add($registry);
    }

    public function testGetReturnsInstancesItem()
    {
        $locator  = new Services();
        $instance = new \stdClass();
        $locator->set('foo', $instance);
        $this->assertSame($instance, $locator->get('foo'));
    }

    public function testGetFromFactory()
    {
        $locator = new Services();
        $locator->set('foo', __CLASS__ . '::make');
        $this->assertInstanceOf('stdClass', $locator->get('foo'));
    }

    public function testGetFromAbstractFactory()
    {
        $locator = new Services();
        $locator->set('Foo', __CLASS__ . '::makeAbstract::');
        $this->assertEquals('FooInstance', $locator->get('Foo'));
    }

    public function testGetThrowsExceptionIfServiceNotFound()
    {
        $locator = new Services();
        $this->setExpectedException(ServiceNotFoundException::CLASS);
        $locator->get('foo');
    }

    public function testGetThrowsExceptionIfFailedBuildService()
    {
        $locator = new Services();
        $locator->set('foo', 'foo');
        $this->setExpectedException('RuntimeException');
        $locator->get('foo');
    }

    public function testHasReturnsFalseIfServiceNotExists()
    {
        $locator = new Services();
        $this->assertFalse($locator->has('foo'));
    }

    public function testHasReturnsTrueIfRegistryItemExists()
    {
        $locator = new Services();
        $locator->set('foo', 'foo');
        $this->assertTrue($locator->has('foo'));
    }

    public function testHasReturnsTrueIfInstancesItemExists()
    {
        $locator = new Services();
        $locator->set('foo', new \stdClass());
        $this->assertTrue($locator->has('foo'));
    }

    public function testBuildClass()
    {
        $locator = new Services();
        $this->assertInstanceOf('stdClass', $locator->build('stdClass'));
    }

    public function testBuildUsingFactoryMethod()
    {
        $locator = new Services();
        $this->assertInstanceOf(
            'stdClass',
            $locator->build(__CLASS__ . '::make')
        );
    }

    public function testBuildUsingAbstractFactoryMethod()
    {
        $locator = new Services();
        $this->assertEquals(
            'FooInstance',
            $locator->build(__CLASS__ . '::makeAbstract::', 'Foo')
        );
    }

    public function testBuildThrowsExceptionIfFactoryNotFound()
    {
        $locator = new Services();
        $this->setExpectedException('RuntimeException');
        $locator->build('Foo::make');
    }

    public function testBuildThrowsExceptionIfFactoryMethodIsNotCallable()
    {
        $locator = new Services();
        $this->setExpectedException('RuntimeException');
        $locator->build(__CLASS__ . '::privateMake');
    }

    public function testBuildThrowsExceptionIfClassNotExists()
    {
        $locator = new Services();
        $this->setExpectedException('RuntimeException');
        $locator->build('Foo\Bar\Baz');
    }

    public function testSerializable()
    {
        $locator = new Services();
        $locator->set('foo', 'bar');
        $serialized = serialize($locator);
        $this->assertEquals($locator, unserialize($serialized));
    }

    public function testGetRegistry()
    {
        $config = [
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ];
        $locator = new Services();
        $locator->add($config);

        $this->assertSame($config, $locator->getRegistry());
    }

    public function testGetInstances()
    {
        $config = [
            'foo' => new \stdClass(),
            'bar' => new \stdClass(),
            'baz' => new \stdClass(),
        ];
        $locator = new Services();

        foreach ($config as $key => $item) {
            $locator->set($key, $item);
        }
        $this->assertSame($config, $locator->getInstances());
    }

    protected function extractRegistry($locator)
    {
        $reflection = new ReflectionProperty($locator, 'registry');
        $reflection->setAccessible(true);

        return $reflection->getValue($locator);
    }

    protected function extractInstances($locator)
    {
        $reflection = new ReflectionProperty($locator, 'instances');
        $reflection->setAccessible(true);

        return $reflection->getValue($locator);
    }
}
