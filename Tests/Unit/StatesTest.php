<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Structures\Collection;

class StatesTest extends TestCase 
{
    protected IStateContainer $stateContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateContainer = $this->container()->get(IStateContainer::class);
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

    public function testAddAfterMethod(): void
    {
        $mock = $this->createMock(State::class);
        $child = $this->getMockBuilder(State::class)
            ->setMockClassName($mock::class . '_child')
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
}