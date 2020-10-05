<?php

namespace Tests\Unit;

use stdClass;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Implementation\ServiceContainer;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Implementation\Exceptions\BadParamsToDependencyInjection;

class ServiceContainerTest extends TestCase
{
    /**
     * Fake class from stdClass
     */
    protected string $fakeClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClass = $this->getMockClass(stdClass::class);
    }

    /**
     * @test
     */
    public function initDI()
    {
        $this->assertTrue(function_exists('\Phox\Nebula\Atom\Files\init'));
        $this->assertTrue(function_exists('container'));
        $result = container();
        $this->assertInstanceOf(IDependencyInjection::class, $result);
    }

    /**
     * @test
     */
    public function basicGet()
    {
        $object = container()->get($this->fakeClass);
        $this->assertInstanceOf($this->fakeClass, $object);
    }

    /**
     * @test
     */
    public function basicMake()
    {
        $object = container()->make($this->fakeClass);
        $this->assertInstanceOf($this->fakeClass, $object);
    }

    /**
     * @test
     */
    public function basicCall()
    {
        $fakeObject = $this->getMockBuilder($this->fakeClass)->addMethods(['call', 'callStatic'])->getMock();
        $fakeObject->method('call')->willReturn(true);
        $this->assertTrue(container()->call([$fakeObject, 'call']));
    }

    /**
     * @test
     */
    public function singleton()
    {
        $object = new stdClass;
        container()->singleton($object);
        $this->assertSame($object, container()->get(stdClass::class));
        $this->assertNotSame($object, container()->make(stdClass::class));
        container()->singleton($this->createMock(stdClass::class), stdClass::class);
        $singleton = container()->get(stdClass::class);
        $this->assertNotSame($object, $singleton);
        $this->assertInstanceOf(stdClass::class, $singleton);
    }

    /**
     * @test
     */
    public function transient()
    {
        container()->transient(stdClass::class, stdClass::class);
        $this->assertEquals(new stdClass, container()->get(stdClass::class));
        $this->assertEquals(new stdClass, container()->make(stdClass::class));
        $mockClass = $this->getMockClass(stdClass::class);
        container()->transient($mockClass, stdClass::class);
        $transient = container()->get(stdClass::class);
        $this->assertNotEquals(new stdClass, $transient);
        $this->assertEquals(new $mockClass, $transient);
        $this->assertInstanceOf(stdClass::class, $transient);
    }

    /**
     * @test
     */
    public function callCallback()
    {
        $this->assertTrue(container()->call(fn() => true));
        $this->assertInstanceOf(
            stdClass::class,
            container()->call(fn(stdClass $obj) => $obj)
        );
        $object = new stdClass;
        $this->assertNotSame(
            $object,
            container()->call(fn(stdClass $obj) => $obj)
        );
        container()->singleton($object);
        $this->assertSame(
            $object,
            container()->call(fn(stdClass $obj) => $obj)
        );
    }

    /**
     * @test
     */
    public function callWithParams()
    {
        $this->assertEquals(
            'default',
            container()->call(fn(string $some = 'default') => $some)
        );
        $this->assertNull(container()->call(fn(?string $some) => $some));
        $this->assertNull(container()->call(fn($some) => $some));
        $this->expectException(BadParamsToDependencyInjection::class);
        container()->call(fn(string $some) => $some);
    }

    /**
     * @test
     */
    public function replaceOriginalDI()
    {
        $mock = $this->getMockBuilder(IDependencyInjection::class)->getMock();
        container()->singleton($mock, IDependencyInjection::class);
        $container = container()->get(IDependencyInjection::class);
        $this->assertSame($mock, $container);
        $this->assertNotInstanceOf(ServiceContainer::class, $container);
    }

    /**
     * @test
     */
    public function callInvokeableObject()
    {
        $object = new class {
            public function __invoke() {
                return "Work!";
            }
        };
        $this->assertEquals("Work!", call($object));
    }
}