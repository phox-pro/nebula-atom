<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Events\StateRegisteredEvent;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Implementation\StateContainer;
use Phox\Nebula\Atom\Implementation\States\InitState;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\TestCase;
use Phox\Structures\Collection;
use stdClass;

class StatesTest extends TestCase 
{
    protected IStateContainer $stateContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateContainer = $this->container()->get(IStateContainer::class);
    }

    public function testSingletonContainer(): void
    {
        $this->assertIsSingleton(IStateContainer::class);
        $this->assertInstanceOf(StateContainer::class, $this->container()->get(IStateContainer::class));
    }

    public function testAddMethod(): void
    {
        $mock = $this->createMock(State::class);

        $this->stateContainer->add($mock);

        $this->assertTrue($this->stateContainer->getRoot()->contains($mock));
    }

    public function testStateExistsError(): void
    {
        $mock = $this->createMock(State::class);

        $this->stateContainer->add($mock);

        $this->expectException(StateExistsException::class);
        $this->stateContainer->add($mock);
    }

    public function testStateExistsAtChildren(): void
    {
        $mock = $this->createMock(State::class);

        $this->stateContainer->addAfter($mock, InitState::class);

        $this->expectException(StateExistsException::class);
        $this->stateContainer->addAfter($mock, InitState::class);
    }

    public function testStateExistsAtChildrenForRoot(): void
    {
        $mock = $this->createMock(State::class);

        $this->stateContainer->addAfter($mock, InitState::class);

        $this->expectException(StateExistsException::class);
        $this->stateContainer->add($mock);
    }

    public function testAddAfterMethod(): void
    {
        $mock = $this->createMock(State::class);
        $child = $this->getMockBuilder(State::class)
            ->setMockClassName($mock::class . '_child')
            ->setConstructorArgs([$this->container()])
            ->getMock();

        $this->stateContainer->add($mock);
        $this->stateContainer->addAfter($child, $mock::class);

        $rootStates = $this->stateContainer->getRoot();

        $this->assertTrue($rootStates->contains($mock));
        $this->assertFalse($rootStates->contains($child));

        $children = $this->stateContainer->getChildren($mock::class);

        $this->assertInstanceOf(Collection::class, $children);
        $this->assertTrue($children->contains($child));
    }

    public function testStatesAsEvents(): void
    {
        $state = $this->createMock(State::class);

        $this->assertTrue($state instanceof IEvent);
    }

    public function testRegisterStatesByEvent(): void
    {
        $mock = $this->createMock(State::class);
        $child = $this->getMockBuilder(State::class)
            ->setMockClassName($mock::class . '_child')
            ->setConstructorArgs([$this->container()])
            ->getMock();

        $this->stateContainer->eStateRegistered->listen(function (StateRegisteredEvent $event, IStateContainer $container) use ($mock, $child) {
            $state = $event->getState();

            if ($state instanceof $mock) {
                $container->addAfter($child, $mock::class);
            }
        });

        $this->assertNull($this->stateContainer->getState($child::class));

        $this->stateContainer->add($mock);

        $this->assertNotNull($this->stateContainer->getState($child::class));
    }

    public function testLazyAddAfterMethod(): void
    {
        $mock = $this->createMock(State::class);
        $child = $this->getMockBuilder(State::class)
            ->setMockClassName($mock::class . '_child')
            ->setConstructorArgs([$this->container()])
            ->getMock();

        $this->stateContainer->addAfter($child, $mock::class);

        $this->assertNull($this->stateContainer->getState($child::class));

        $this->stateContainer->add($mock);

        $this->assertNotNull($this->stateContainer->getState($child::class));
    }

    public function testFallbackAtAddAfterMethod(): void
    {
        $mock = $this->createMock(State::class);
        $child = $this->getMockBuilder(State::class)
            ->setMockClassName($mock::class . '_child')
            ->setConstructorArgs([$this->container()])
            ->getMock();

        $fallbackMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['testFallback'])
            ->getMock();
        $fallbackMock->expects($this->once())->method('testFallback');

        $this->stateContainer->addAfter($child, $mock::class, [$fallbackMock, 'testFallback']);

        $this->nebula->run();
    }
}