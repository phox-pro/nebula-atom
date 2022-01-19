<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\AtomProvider;
use Phox\Nebula\Atom\Implementation\BasicEvent;
use Phox\Nebula\Atom\Implementation\Exceptions\AnotherInjectionExists;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IProvidersContainer;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Structures\ObjectCollection;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

class ApplicationTest extends TestCase 
{
    public function testApplicationInDI(): void
    {
        $this->assertSame($this->nebula, $this->container()->get(Application::class));
    }

    public function testCanAddProviders(): void
    {
        $providersContainer = $this->container()->get(IProvidersContainer::class);
        $providers = $providersContainer->getProviders();

        $this->assertInstanceOf(ObjectCollection::class, $providers);
        $this->assertEquals(1, $providers->count());
        $this->assertInstanceOf(AtomProvider::class, $providers->first());

        $provider = new class extends Provider {};
        $providersContainer->addProvider($provider);

        $this->assertTrue($providers->contains($provider));
    }

    public function testApplicationCallProvider(): void
    {
        /** @var Provider|MockObject $provider */
        $provider = $this->getMockBuilder(Provider::class)->addMethods(['__invoke'])->getMock();
        $provider->expects($this->once())->method('__invoke');

        $this->container()->get(IProvidersContainer::class)->addProvider($provider);
    }

    /**
     * @throws AnotherInjectionExists
     */
    public function testRegisterStatesFromProvider(): void
    {
        $state = new class extends State {};
        $state->listen(fn(IEvent $event) => $this->assertInstanceOf($state::class, $event));

        $provider = new class($state) extends Provider {
            public function __construct(private State $state) {}

            public function __invoke(IStateContainer $stateContainer): void
            {
                $stateContainer->add($this->state);
            }
        };

        $this->container()->get(IProvidersContainer::class)->addProvider($provider);
        $this->nebula->run();
    }

    /**
     * @throws AnotherInjectionExists
     */
    public function testApplicationInitEvent(): void
    {
        $mock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['callMe'])
            ->getMock();
        $mock->expects($this->once())->method('callMe');

        $this->nebula->eInit->listen([$mock, 'callMe']);

        $this->nebula->run();
    }

    /**
     * @throws AnotherInjectionExists
     */
    public function testApplicationEventsAsParam(): void
    {
        $mock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['callMe'])
            ->getMock();
        $mock->expects($this->exactly(2))->method('callMe');

        $initListener = fn(Application $application, IEvent $event) => $this->assertInstanceOf(BasicEvent::class, $event);
        $completedListener = fn(IEvent $event) => $this->assertInstanceOf(BasicEvent::class, $event);

        $this->nebula->eInit->listen([$mock, 'callMe']);
        $this->nebula->eCompleted->listen([$mock, 'callMe']);

        $this->nebula->eInit->listen($initListener);
        $this->nebula->eCompleted->listen($completedListener);

        $this->nebula->run();
    }
}