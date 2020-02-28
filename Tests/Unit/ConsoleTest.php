<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Exceptions\ConsoleException;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\Implementation\States\ConsoleState;
use stdClass;

class ConsoleTest extends TestCase 
{
    protected IStateContainer $stateContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateContainer = get(IStateContainer::class);
        ConsoleState::getListeners()->clear();
    }

    /**
     * @test
     */
    public function consoleStateTest()
    {
        $this->assertFalse($this->stateContainer->getAll()->has(ConsoleState::class));
        $mock = $this->getMockBuilder(stdClass::class)->addMethods(['run'])->getMock();
        $mock->expects($this->once())->method('run');
        ConsoleState::listen([$mock, 'run']);
        $this->expectException(ConsoleException::class);
        app()->runConsole([]);
    }

    /**
     * @test
     */
    public function withoutCommandTest()
    {
        $this->expectException(ConsoleException::class);
        $this->expectExceptionMessage('Atom command is required');
        app()->runConsole(['file']);
    }

    /**
     * @test
     */
    public function badCommandTest()
    {
        $this->expectException(ConsoleException::class);
        $this->expectExceptionMessage("Command must match '{module}::{command}' pattern, 'command' was given");
        app()->runConsole(['file', 'command']);
    }
}