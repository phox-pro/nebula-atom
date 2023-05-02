<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Services\ServiceContainer;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerFacade;
use Phox\Nebula\Atom\Notion\IServiceContainer;
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

    /**
     * @throws Exception
     */
    public function testCallableAsService(): void
    {
        $container = new ServiceContainer();
        $transientCallCount = 4;

        $testSingletonRegisterMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['test'])
            ->getMock();
        $testTransientRegisterMock = $this->getMockBuilder(stdClass::class)
            ->setMockClassName(stdClass::class . 'transient')
            ->addMethods(['test'])
            ->getMock();

        $testSingletonRegisterMock->expects($this->once())->method('test')->willReturn($this);
        $testTransientRegisterMock->expects($this->exactly($transientCallCount))
            ->method('test')
            ->willReturn($this);

        $container->singleton([$testSingletonRegisterMock, 'test'], $testSingletonRegisterMock::class);
        $container->transient([$testTransientRegisterMock, 'test'], $testTransientRegisterMock::class);

        for ($i = 0; $i < $transientCallCount; $i++) {
            $container->get($testTransientRegisterMock::class);
            $container->get($testSingletonRegisterMock::class);
        }
    }

    /**
     * @throws Exception
     */
    public function testServiceContainerCallableReturnExpectedValue(): void
    {
        $container = new ServiceContainer();
        $containerMock = $this->createMock(IServiceContainer::class);

        $container->singleton(
            fn(IServiceContainer $container): IServiceContainer => $containerMock,
            IServiceContainer::class
        );

        $result = $container->get(IServiceContainer::class);

        $this->assertSame($containerMock, $result);
    }
}