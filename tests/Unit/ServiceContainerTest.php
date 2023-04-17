<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Services\ServiceContainer;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerFacade;
use Phox\Nebula\Atom\TestCase;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;

class ServiceContainerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testBasicGet(): void
    {
        $container = new ServiceContainer();

        $fakeClass = $this->createMock(stdClass::class)::class;
        $object = $container->get($fakeClass);

        $this->assertInstanceOf($fakeClass, $object);
    }

    /**
     * @throws Exception
     */
    public function testBasicMake(): void
    {
        $container = new ServiceContainer();

        $fakeClass = $this->createMock(stdClass::class)::class;
        $object = $container->make($fakeClass);

        $this->assertInstanceOf($fakeClass, $object);
    }

    public function testSingleton(): void
    {
        $container = new ServiceContainer();
        $object = new stdClass();
        $container->singleton($object);

        $this->assertSame($object, $container->get(stdClass::class));
        $this->assertNotSame($object, $container->make(stdClass::class));

        $container->singleton($this->createMock(stdClass::class), stdClass::class);
        $singleton = $container->get(stdClass::class);

        $this->assertNotSame($singleton, $object);
        $this->assertInstanceOf(stdClass::class, $singleton);
    }

    public function testFacade(): void
    {
        $container = new ServiceContainer();
        ServiceContainerFacade::setContainer($container);

        $this->assertSame($container, ServiceContainerFacade::instance());

        ServiceContainerFacade::setContainer(new ServiceContainer());

        $this->assertNotSame($container, ServiceContainerFacade::instance());
    }
}