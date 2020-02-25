<?php

namespace Tests\Unit;

use Exception;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Notion\Interfaces\IState;
use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\Exceptions\MustImplementInterface;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\Notion\Traits\TEvent;
use stdClass;

class StatesTest extends TestCase 
{
    protected IStateContainer $stateContainer;

    protected function setUp(): void
    {
        $this->stateContainer = get(IStateContainer::class);
        parent::setUp();
    }

    /**
     * @test
     */
    public function dependencyTest()
    {
        $container = get(IStateContainer::class);
        $this->assertInstanceOf(IStateContainer::class, $container);
    }

    /**
     * @test
     */
    public function listTest()
    {
        $all = $this->stateContainer->getAll();
        $root = $this->stateContainer->getRoot();
        $this->assertInstanceOf(Collection::class, $all);
        $this->assertInstanceOf(Collection::class, $root);
        $this->assertEquals('string', $all->getType());
    }

    /**
     * @test
     */
    public function addTest()
    {
        $mockClass = $this->getMockClass(IState::class);
        $this->stateContainer->add($mockClass);
        $this->assertEquals([$mockClass], $this->stateContainer->getAll()->all());
        $this->assertEquals([$mockClass], $this->stateContainer->getRoot()->all());
    }

    /**
     * @test
     */
    public function badStateClass()
    {
        $mockClass = $this->getMockClass(stdClass::class);
        $this->expectException(MustImplementInterface::class);
        $this->stateContainer->add($mockClass);
    }

    /**
     * @test
     */
    public function stateExistsError()
    {
        $mockClass = $this->getMockClass(IState::class);
        $this->stateContainer->add($mockClass);
        $this->expectException(StateExistsException::class);
        $this->stateContainer->add($mockClass);
    }

    /**
     * @test
     */
    public function addAfterTest()
    {
        $mockClass = $this->getMockClass(IState::class);
        $child = $this->getMockClass(IState::class, [], [], $mockClass . '_child');
        $this->stateContainer->add($mockClass);
        $this->stateContainer->addAfter($child, $mockClass);
        $this->assertEquals([$mockClass], $this->stateContainer->getRoot()->all());
        $this->assertEquals([$mockClass, $child], $this->stateContainer->getAll()->all());
        $children = $this->stateContainer->getChildren($mockClass);
        $this->assertInstanceOf(Collection::class, $children);
        $this->assertEquals([$child], $children->all());
    }

    /**
     * @test
     */
    public function statesAsEvents()
    {
        $stateClass = get_class(new class implements IState { 
            use TEvent;

            public function execute()
            {
                
            }
        });
        $errorMessage = 'State can be used as Event';
        $stateClass::listen(fn () => error(Exception::class, $errorMessage));
        $this->expectExceptionMessage($errorMessage);
        $stateClass::notify();
    }
}