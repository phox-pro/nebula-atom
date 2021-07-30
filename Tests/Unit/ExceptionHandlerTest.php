<?php

namespace Tests\Unit;

use Exception;
use Phox\Nebula\Atom\Implementation\Exceptions\BadCollectionType;
use Phox\Nebula\Atom\Implementation\Exceptions\CollectionHasKey;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Implementation\ExceptionHandler;

class ExceptionHandlerTest extends TestCase 
{
    public function testInstanceIsSingleton(): void
    {
        $this->assertInstanceOf(ExceptionHandler::class, $this->container()->get(ExceptionHandler::class));
        $this->assertIsSingleton(ExceptionHandler::class);
    }

    /**
     * @throws CollectionHasKey
     * @throws BadCollectionType
     */
    public function testHandlerBasic(): void
    {
        $handler = $this->container()->get(ExceptionHandler::class);

        $testException = new class extends Exception {};
        $testExceptionClass = $testException::class;

        $handler->listen(fn(Exception $exception): mixed => $this->assertSame($testException, $exception), $testExceptionClass);

        $handler->execute($testException);
    }
}