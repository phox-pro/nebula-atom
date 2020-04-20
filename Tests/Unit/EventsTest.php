<?php

namespace Tests\Unit;

use Error;
use Exception;
use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Notion\Abstracts\Event;
use stdClass;

class EventsTest extends TestCase 
{
    /**
     * @test
     */
    public function abstractionIsAvailable()
    {
        $this->assertTrue(class_exists(Event::class));
        $this->expectException(Error::class);
        new Event;       
    }

    /**
     * @test
     */
    public function addListeners()
    {
        $mockClass = get_class(new class extends Event { protected static Collection $listeners; });
        $listener = fn () => '';
        $mockClass::listen($listener);
        $this->assertTrue($mockClass::getListeners()->has($listener));
    }

    /**
     * @test
     */
    public function notifyListeners()
    {
        $mockClass = get_class(new class extends Event { protected static Collection $listeners; });
        $exceptionClass = get_class(new class extends Exception {});
        $listener = fn () => error($exceptionClass);
        $mockClass::listen($listener);
        $this->expectException($exceptionClass);
        $mockClass::notify();
    }

    /**
     * @test
     */
    public function listenerWithParams()
    {
        $mockClass = get_class(new class extends Event { protected static Collection $listeners; });
        $mockObject = $this->getMockBuilder(stdClass::class)->addMethods(['test'])->getMock();
        $mockObject->expects($this->once())->method('test');
        $mockClass::listen(fn ($testObject) => $testObject->test());
        $mockClass::notify($mockObject);
    }

    /**
     * @test
     */
    public function listenerWithNamedParams()
    {
        $mockClass = get_class(new class extends Event { protected static Collection $listeners; });
        $mockObject = $this->getMockBuilder(stdClass::class)->addMethods(['test'])->getMock();
        $moreMock = $this->getMockBuilder(stdClass::class)->addMethods(['test'])->getMock();
        $mockObject->expects($this->never())->method('test');
        $moreMock->expects($this->once())->method('test');
        $mockClass::listen(fn ($testObject, $moreObject) => $moreObject->test());
        $mockClass::notifyRaw([
            'moreObject' => $moreMock
        ]);
    }
}