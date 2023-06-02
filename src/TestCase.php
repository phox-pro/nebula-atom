<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\Event\Event;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerAccess;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerFacade;
use Phox\Nebula\Atom\Implementation\State\State;
use Phox\Nebula\Atom\Notion\IEvent;
use Phox\Nebula\Atom\Notion\IProvider;
use Phox\Nebula\Atom\Notion\IProviderContainer;
use Phox\Nebula\Atom\Notion\IServiceContainer;
use Phox\Nebula\Atom\Notion\IStateContainer;
use PHPUnit\Framework\TestCase as ParentTestCase;
use stdClass;

class TestCase extends ParentTestCase
{
    use ServiceContainerAccess;

    protected function setUp(): void
    {
        parent::setUp();

        ServiceContainerFacade::instance()?->reset();
        Event::clearListeners();
    }

    /**
     * @param class-string<IEvent> $eventClass
     * @return void
     */
    protected function assertEventWillFire(string $eventClass): void
    {
        $eventListenerMock = $this->getMockBuilder(stdClass::class)->addMethods(['listen'])->getMock();
        $eventListenerMock->expects($this->atLeastOnce())->method('listen');

        $eventClass::listen([$eventListenerMock, 'listen']);
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

    /**
     * @param class-string<IProvider> $providerClass
     * @param string $message
     * @return void
     */
    final public static function assertProviderExists(string $providerClass, string $message = ''): void
    {
        static::assertContains(
            $providerClass,
            static::getProvidersAsArrayOfClasses(),
            $message,
        );
    }

    /**
     * @param class-string<IProvider> $providerClass
     * @param string $message
     * @return void
     */
    final public static function assertProviderNotExists(string $providerClass, string $message = ''): void
    {
        static::assertNotContains(
            $providerClass,
            static::getProvidersAsArrayOfClasses(),
            $message,
        );
    }

    /**
     * @param class-string<State> $stateClass
     * @param string $message
     * @return void
     */
    final public static function assertStateExists(string $stateClass, string $message = ''): void
    {
        static::assertNotNull(
            ServiceContainerFacade::get(IStateContainer::class)->getState($stateClass),
            $message,
        );
    }

    /**
     * @param class-string<State> $stateClass
     * @param string $message
     * @return void
     */
    final public static function assertStateNotExists(string $stateClass, string $message = ''): void
    {
        static::assertNull(
            ServiceContainerFacade::get(IStateContainer::class)->getState($stateClass),
            $message,
        );
    }

    /**
     * @return array<string>
     */
    final protected static function getProvidersAsArrayOfClasses(): array
    {
        $providersCollection = ServiceContainerFacade::get(IProviderContainer::class)->getProviders();
        $providers = [];

        foreach ($providersCollection as $provider) {
            $providers[] = $provider::class;
        }

        return $providers;
    }
}