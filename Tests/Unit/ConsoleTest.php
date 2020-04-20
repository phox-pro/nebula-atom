<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\Console;
use Phox\Nebula\Atom\Implementation\Exceptions\ConsoleException;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\Implementation\States\ConsoleState;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Interfaces\ICommand;
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

    /**
     * @test
     */
    public function registerTest()
    {
        $mock = $this->getMockBuilder(stdClass::class)->addMethods(['call'])->getMock();
        $mock->expects($this->once())->method('call');
        container()->singleton($mock, stdClass::class);
        $provider = new class extends Provider {
            public function define(Console $console) {
                $console->registerCommand(get_class(new class(new stdClass) implements ICommand {
                    public static string $module = 'atom';
                    public static string $command = 'unittestcommand';

                    private stdClass $mock;
                    
                    public function __construct(stdClass $mock) {
                        $this->mock = $mock;
                    }

                    public function run() {
                        call([$this->mock, 'call']);
                    }
                }));
            }
        };
        app()->addProvider($provider);
        app()->runConsole(['file', 'atom::unittestcommand']);
    }

    /**
     * @test
     */
    public function optionsTest()
    {
        $mock = $this->getMockBuilder(stdClass::class)->getMock();
        $mock->testCase = $this;
        container()->singleton($mock, stdClass::class);
        $provider = new class extends Provider {
            public function define(Console $console) {
                $console->registerCommand(get_class(new class(new stdClass) implements ICommand {
                    public static string $module = 'atom';
                    public static string $command = 'unittestcommand';

                    private stdClass $mock;
                    
                    public function __construct(stdClass $mock) {
                        $this->mock = $mock;
                    }

                    public function run() {
                        $console = get(Console::class);
                        $this->mock->testCase->assertTrue($console->hasOption('method1') && $console->option('method1') == true && $console->option('method1') === '1');
                        $this->mock->testCase->assertTrue($console->hasOption('method3'));
                        $this->mock->testCase->assertFalse($console->hasOption('method2'));
                        $this->mock->testCase->assertEquals('string', $console->option('q'));
                        $this->mock->testCase->assertEquals('string with space', $console->option('qwerty'));
                        $this->mock->testCase->assertInstanceOf(Collection::class, $console->getOptions());
                    }
                }));
            }
        };
        app()->addProvider($provider);
        app()->runConsole(['file', 'atom::unittestcommand', '-q', 'string', '--qwerty', 'string with space', '--method1', '--method3']);
    }
}