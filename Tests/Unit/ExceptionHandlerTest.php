<?php

namespace Tests\Unit;

use Exception;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Implementation\ExceptionHandler;

class ExceptionHandlerTest extends TestCase 
{
    /**
     * @test
     */
    public function instanceTest()
    {
        $this->assertInstanceOf(ExceptionHandler::class, get(ExceptionHandler::class));
        $this->assertIsSingleton(ExceptionHandler::class);
    }

    /**
     * @test
     */
    public function handlerTest()
    {
        $testException = new class extends Exception {};
        $testExceptionClass = get_class($testException);
        ExceptionHandler::listen(fn ($exception) => $this->assertSame($testException, $exception), $testExceptionClass);
        exceptionHandler()->execute($testException);
    }
}