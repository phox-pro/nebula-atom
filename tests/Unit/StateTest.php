<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Implementation\State\State;
use Phox\Nebula\Atom\Implementation\State\StateContainer;
use Phox\Nebula\Atom\Notion\IEvent;
use Phox\Nebula\Atom\Notion\IStateContainer;
use Phox\Nebula\Atom\TestCase;
use Phox\Structures\Interfaces\ICollection;
use PHPUnit\Framework\MockObject\Exception;

class StateTest extends TestCase
{
    protected IStateContainer $stateContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateContainer = new StateContainer();
    }

    /**
     * @throws StateExistsException
     * @throws Exception
     */
    public function testAddMethod(): void
    {
        $mock = $this->createMock(State::class);

        $this->stateContainer->add($mock);

        $this->assertTrue($this->stateContainer->getRoot()->contains($mock));
    }

    /**
     * @throws StateExistsException
     * @throws Exception
     */
    public function testStateExistsError(): void
    {
        $mock = $this->createMock(State::class);

        $this->stateContainer->add($mock);

        $this->expectException(StateExistsException::class);
        $this->stateContainer->add($mock);
    }

    /**
     * @throws StateExistsException
     * @throws Exception
     */
    public function testStateExistsAtChildren(): void
    {
        $mock = $this->createMock(State::class);
        $parentState = $this->getMockBuilder(State::class)
            ->setMockClassName($mock::class . '_parent')
            ->getMock();

        $this->stateContainer->add($parentState);
        $this->stateContainer->addAfter($mock, $parentState::class);

        $this->expectException(StateExistsException::class);
        $this->stateContainer->addAfter($mock, $parentState::class);
    }

    /**
     * @throws StateExistsException
     * @throws Exception
     */
    public function testStateExistsAtChildrenForRoot(): void
    {
        $mock = $this->createMock(State::class);
        $parentState = $this->getMockBuilder(State::class)
            ->setMockClassName($mock::class . '_parent')
            ->getMock();

        $this->stateContainer->add($parentState);
        $this->stateContainer->addAfter($mock, $parentState::class);

        $this->expectException(StateExistsException::class);
        $this->stateContainer->add($mock);
    }

    /**
     * @throws StateExistsException
     * @throws Exception
     */
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

        $this->assertInstanceOf(ICollection::class, $children);
        $this->assertTrue($children->contains($child));
    }

    /**
     * @throws Exception
     */
    public function testStatesAsEvents(): void
    {
        $state = $this->createMock(State::class);

        $this->assertTrue($state instanceof IEvent);
    }

    /**
     * @throws StateExistsException
     * @throws Exception
     */
    public function testLazyAddAfterMethod(): void
    {
        $mock = $this->createMock(State::class);
        $child = $this->getMockBuilder(State::class)
            ->setMockClassName($mock::class . '_child')
            ->getMock();

        $this->stateContainer->addAfter($child, $mock::class);

        $this->assertNull($this->stateContainer->getState($child::class));

        $this->stateContainer->add($mock);

        $this->assertNotNull($this->stateContainer->getState($child::class));
    }
}