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

use Es\Services\Services;

class ServicesTest extends \PHPUnit_Framework_TestCase
{
    public function testMergeRegistry()
    {
        $targetConfig = [
            'foo' => 'foo',
            'bar' => 'foo',
        ];
        $target = new Services();
        $target->add($targetConfig);

        $sourceConfig = [
            'bar' => 'bar',
            'baz' => 'baz',
        ];
        $source = new Services();
        $source->add($sourceConfig);

        $return = $target->merge($source);
        $this->assertSame($return, $target);

        $expected = [
            'foo' => $targetConfig['foo'],
            'bar' => $sourceConfig['bar'],
            'baz' => $sourceConfig['baz'],
        ];
        $this->assertSame($expected, $target->getRegistry());
    }

    public function testMergeInstances()
    {
        $targetConfig = [
            'foo' => new \stdClass(),
            'bar' => new \stdClass(),
        ];
        $target = new Services();
        foreach ($targetConfig as $key => $item) {
            $target->set($key, $item);
        }

        $sourceConfig = [
            'bar' => new \stdClass(),
            'baz' => new \stdClass(),
        ];
        $source = new Services();
        foreach ($sourceConfig as $key => $item) {
            $source->set($key, $item);
        }

        $return = $target->merge($source);
        $this->assertSame($return, $target);

        $expected = [
            'foo' => $targetConfig['foo'],
            'bar' => $sourceConfig['bar'],
            'baz' => $sourceConfig['baz'],
        ];
        $this->assertSame($expected, $target->getInstances());
    }
}
