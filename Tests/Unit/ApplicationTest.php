<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Implementation\AtomProvider;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerFacade;
use Phox\Nebula\Atom\Implementation\StartupConfiguration;
use Phox\Nebula\Atom\Implementation\State\State;
use Phox\Nebula\Atom\Notion\IProvider;
use Phox\Nebula\Atom\Notion\IProviderContainer;
use Phox\Nebula\Atom\Notion\IStateContainer;
use Phox\Nebula\Atom\TestCase;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;

class ApplicationTest extends TestCase
{
    public function testApplicationSingleton(): void
    {
        $app = new Application();

        $this->assertSame($app, ServiceContainerFacade::get(Application::class));
    }

    /**
     * @throws Exception
     */
    public function testApplicationRegisterProviders(): void
    {
        $app = new Application();
        $providersContainer = ServiceContainerFacade::get(IProviderContainer::class);
        $providerMock = $this->createMock(IProvider::class);

        $providerMock->expects($this->once())->method('register');
        $providersContainer->addProvider($providerMock);

        $app->run();
    }

    /**
     * @throws Exception
     */
    public function testApplicationRunStates(): void
    {
        $app = new Application();
        $statesContainer = ServiceContainerFacade::get(IStateContainer::class);
        $stateMock = $this->createMock(State::class);

        $stateMock->expects($this->once())->method('notify');
        $statesContainer->add($stateMock);

        $app->run();
    }

    public function testApplicationRegisterProvider(): void
    {
        $app = new Application();

        $providers = ServiceContainerFacade::get(IProviderContainer::class)->getProviders();

        $this->assertCount(1, $providers);
        $this->assertContainsOnlyInstancesOf(AtomProvider::class, $providers);
    }

    public function testApplicationSkipRegisterProviders(): void
    {
        $app = new Application(new StartupConfiguration(
            registerProvidersFromPackages: false,
        ));

        $providers = ServiceContainerFacade::get(IProviderContainer::class)->getProviders();

        $this->assertCount(0, $providers);
    }

    /**
     * @throws Exception
     */
    public function testStateFallbackCalled(): void
    {
        $app = new Application();

        $parent = $this->createMock(State::class);
        $child = $this->getMockBuilder(State::class)
            ->setMockClassName($parent::class . '_child')
            ->getMock();
        $mock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['fallback'])
            ->getMock();

        $mock->expects($this->once())->method('fallback');

        $states = ServiceContainerFacade::get(IStateContainer::class);
        $states->addAfter($child, $parent::class, [$mock, 'fallback']);

        $app->run();
    }
}