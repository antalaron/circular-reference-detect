<?php

/*
 * (c) Antal Áron <antalaron@antalaron.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antalaron\Component\CircularReferenceDetect\Tests;

use Antalaron\Component\CircularReferenceDetect\CircularReferenceDetect;
use Antalaron\Component\CircularReferenceDetect\Exception\MaximumDepthReachedException;

class CircularReferenceDetectTest extends \PHPUnit_Framework_TestCase
{
    protected $detector;

    public function setUp()
    {
        $this->detector = new CircularReferenceDetect();
    }

    public function setUpWithArg($arg1, $arg2)
    {
        $this->detector = new CircularReferenceDetect($arg1, $arg2);
    }

    public function tearDown()
    {
        unset($this->detector);
    }

    public function testDefaultDepth()
    {
        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('maxDepth');
        $property->setAccessible(true);
        $this->assertSame(CircularReferenceDetect::MAX_DEPTH, $property->getValue($this->detector));
    }

    public function testCustomDepth()
    {
        $depth = 10;
        $this->setUpWithArg($depth, false);

        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('maxDepth');
        $property->setAccessible(true);
        $this->assertSame($depth, $property->getValue($this->detector));
    }

    public function testSetDepth()
    {
        $depth = 10;
        $this->detector->setMaxDepth($depth);

        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('maxDepth');
        $property->setAccessible(true);
        $this->assertSame($depth, $property->getValue($this->detector));
    }

    public function testResetDepth()
    {
        $depth = 10;
        $this->setUpWithArg($depth, false);
        $this->detector->setMaxDepth();

        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('maxDepth');
        $property->setAccessible(true);
        $this->assertSame(CircularReferenceDetect::MAX_DEPTH, $property->getValue($this->detector));
    }

    public function testDefaultThrow()
    {
        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('throwExceptionOnReachMaxDepth');
        $property->setAccessible(true);
        $this->assertFalse($property->getValue($this->detector));
    }

    public function testCustomThrow()
    {
        $depth = 10;
        $this->setUpWithArg($depth, true);

        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('throwExceptionOnReachMaxDepth');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($this->detector));
    }

    public function testSetThrow()
    {
        $this->detector->setThrowExceptionOnReachMaxDepth(true);

        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('throwExceptionOnReachMaxDepth');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($this->detector));
    }

    public function testResetThrow()
    {
        $depth = 10;
        $this->setUpWithArg($depth, true);
        $this->detector->setThrowExceptionOnReachMaxDepth();

        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('throwExceptionOnReachMaxDepth');
        $property->setAccessible(true);
        $this->assertFalse($property->getValue($this->detector));
    }

    public function testSingleton()
    {
        $depth = 10;
        $this->detector = CircularReferenceDetect::newInstance($depth, true);

        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('maxDepth');
        $property->setAccessible(true);
        $this->assertInstanceOf(CircularReferenceDetect::class, $this->detector);
        $this->assertSame($depth, $property->getValue($this->detector));

        $property = $reflector->getProperty('throwExceptionOnReachMaxDepth');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($this->detector));
    }

    public function testNoCircularReference()
    {
        $a = [
            'a' => ['b'],
            'b' => ['c'],
            'c' => ['d'],
        ];

        $this->assertFalse($this->detector->hasCircularReference($a));
    }

    public function testNoCircularReferenceFoundBeacusePartialSearch()
    {
        $a = [
            'a' => ['b'],
            'b' => ['c'],
            'c' => ['a'],
            'd' => ['e'],
            'e' => ['f'],
        ];
        $b = [
            'd',
            'e',
        ];

        $this->assertFalse($this->detector->hasCircularReference($b, $a));
    }

    public function testHasCircularReference()
    {
        $a = [
            'a' => ['b'],
            'b' => ['c'],
            'c' => ['a'],
        ];

        $return = $this->detector->hasCircularReference($a);

        $this->assertNotFalse($return);
        $this->assertSame(['a', 'b', 'c', 'a'], $return);
    }

    public function testHasCircularReferenceInPartialSearch()
    {
        $a = [
            'a' => ['b'],
            'b' => ['c'],
            'c' => ['a'],
            'd' => ['e'],
            'e' => ['d'],
        ];
        $b = [
            'd',
            'e',
        ];

        $return = $this->detector->hasCircularReference($b, $a);

        $this->assertNotFalse($return);
        $this->assertSame(['d', 'e', 'd'], $return);
    }

    public function testSelfReference()
    {
        $a = [
            'a' => ['a'],
        ];

        $return = $this->detector->hasCircularReference($a);

        $this->assertNotFalse($return);
        $this->assertSame(['a', 'a'], $return);
    }

    public function testReachMaxDepth()
    {
        $a = [
            'a' => ['b'],
            'b' => ['c'],
            'c' => ['a'],
        ];

        $this->detector->setMaxDepth(3);
        $return = $this->detector->hasCircularReference($a);

        $this->assertFalse($return);
    }

    public function testReachMaxDepthWithException()
    {
        $a = [
            'a' => ['b'],
            'b' => ['c'],
            'c' => ['a'],
        ];

        $this->detector->setMaxDepth(3);
        $this->detector->setThrowExceptionOnReachMaxDepth(true);
        $this->expectExceptionWrapper(MaximumDepthReachedException::class);
        $this->detector->hasCircularReference($a);
    }

    public function testReachMaxDepthWithExceptionButShorterCircleFound()
    {
        $a = [
            'a' => ['b'],
            'b' => ['c'],
            'c' => ['a'],
            'd' => ['e'],
            'e' => ['d'],
        ];

        $this->detector->setMaxDepth(3);
        $this->detector->setThrowExceptionOnReachMaxDepth(true);
        $return = $this->detector->hasCircularReference($a);

        $this->assertNotFalse($return);

        $reflector = new \ReflectionClass(CircularReferenceDetect::class);
        $property = $reflector->getProperty('maxDepthReached');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($this->detector));
    }

    protected function expectExceptionWrapper($exception)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($exception);
        } else {
            $this->setExpectedExceptionRegExp($exception, $regexp);
        }
    }
}
