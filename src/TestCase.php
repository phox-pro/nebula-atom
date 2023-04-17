<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\Event\Event;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerFacade;
use Phox\Nebula\Atom\Notion\IServiceContainer;
use PHPUnit\Framework\TestCase as ParentTestCase;

class TestCase extends ParentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ServiceContainerFacade::instance()?->reset();
        Event::clearListeners();
    }

    /**
     * @param class-string $value Class for check
     * @param string $message
     * @return void
     */
    final public static function assertIsSingleton(string $value, string $message = ''): void
    {
        static::assertSame(
            ServiceContainerFacade::get($value),
            ServiceContainerFacade::get($value),
            $message,
        );
    }

    protected function container(): IServiceContainer
    {
        return ServiceContainerFacade::instance();
    }
}