<?php

namespace Tests\Unit;

use Phox\Structures\Abstracts\Type;
use stdClass;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Implementation\ServiceContainer;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Implementation\Exceptions\BadParamsToDependencyInjection;

class ServiceContainerTest extends TestCase
{
    /**
     * Fake class from stdClass
     * @var class-string<stdClass>
     */
    protected string $fakeClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClass = $this->getMockClass(stdClass::class);
    }

    public function testInitDI(): void
    {
        $result = $this->nebula->getDIContainer();

        $this->assertInstanceOf(IDependencyInjection::class, $result);
        $this->assertInstanceOf(ServiceContainer::class, $result);
    }

    public function testBasicGet(): void
    {
        $object = $this->container()->get($this->fakeClass);

        $this->assertInstanceOf($this->fakeClass, $object);
    }

    public function testBasicMake(): void
    {
        $object = $this->container()->make($this->fakeClass);

        $this->assertInstanceOf($this->fakeClass, $object);
    }

    public function testBasicCall(): void
    {
        $fakeObject = $this->getMockBuilder($this->fakeClass)->addMethods(['call'])->getMock();
        $fakeObject->method('call')->willReturn(true);

        $this->assertTrue($this->container()->call([$fakeObject, 'call']));
    }

    public function testSingleton(): void
    {
        $object = new stdClass;
        $this->container()->singleton($object);

        $this->assertSame($object, $this->container()->get(stdClass::class));
        $this->assertNotSame($object, $this->container()->make(stdClass::class));

        $this->container()->singleton($this->createMock(stdClass::class), stdClass::class);
        $singleton = $this->container()->get(stdClass::class);

        $this->assertNotSame($object, $singleton);
        $this->assertInstanceOf(stdClass::class, $singleton);
    }

    public function testTransient(): void
    {
        $this->container()->transient(stdClass::class, stdClass::class);

        $this->assertEquals(new stdClass, $this->container()->get(stdClass::class));
        $this->assertEquals(new stdClass, $this->container()->make(stdClass::class));

        $mockClass = $this->getMockClass(stdClass::class);
        $this->container()->transient($mockClass, stdClass::class);
        $transient = $this->container()->get(stdClass::class);

        $this->assertNotEquals(new stdClass, $transient);
        $this->assertEquals(new $mockClass, $transient);
        $this->assertInstanceOf(stdClass::class, $transient);
    }

    public function testCallCallback(): void
    {
        $this->assertTrue($this->container()->call(fn() => true));

        $this->assertInstanceOf(
            stdClass::class,
            $this->container()->call(fn(stdClass $obj) => $obj)
        );

        $object = new stdClass;

        $this->assertNotSame(
            $object,
            $this->container()->call(fn(stdClass $obj) => $obj)
        );

        $this->container()->singleton($object);

        $this->assertSame(
            $object,
            $this->container()->call(fn(stdClass $obj) => $obj)
        );
    }

    public function testCallWithParams(): void
    {
        $this->assertEquals(
            'default',
            $this->container()->call(fn(string $some = 'default') => $some)
        );

        $this->assertNull($this->container()->call(fn(?string $some) => $some));
        $this->assertNull($this->container()->call(fn($some) => $some));

        $this->expectException(BadParamsToDependencyInjection::class);

        $this->container()->call(fn(string $some) => $some);
    }

    public function testReplaceOriginalDI(): void
    {
        $mock = $this->getMockBuilder(IDependencyInjection::class)->getMock();

        $this->container()->singleton($mock, IDependencyInjection::class);

        $this->assertSame($mock, $this->container());
        $this->assertNotInstanceOf(ServiceContainer::class, $this->container());
    }

    public function testCallInvokeableObject(): void
    {
        $object = new class {
            public function __invoke(): string
            {
                return "Work!";
            }
        };

        $this->assertEquals("Work!", $this->container()->call($object));
    }

    public function testCallUnionTypes(): void
    {
        $callable = fn (int|string|IDependencyInjection $param = 'hello') => $param;

        $result = $this->container()->call($callable);

        $this->assertInstanceOf(IDependencyInjection::class, $result);
    }

    public function testEnumAsSingleton(): void
    {
        $expected = Type::ARRAY;
        $this->container()->singleton($expected);

        $this->assertIsSingleton($expected);
        $this->assertEquals($expected, $this->container()->get(Type::class));
    }

    public function testEnumAsDependency(): void
    {
        $this->expectError();

        $this->container()->call(fn (Type $type): bool => true);
    }
}