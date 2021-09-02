<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\AtomProvider;
use Phox\Nebula\Atom\Implementation\Events\ApplicationCompletedEvent;
use Phox\Nebula\Atom\Implementation\Events\ApplicationInitEvent;
use Phox\Nebula\Atom\Implementation\Exceptions\AnotherInjectionExists;
use Phox\Nebula\Atom\Implementation\Functions;
use Phox\Nebula\Atom\Implementation\ProvidersContainer;
use Phox\Nebula\Atom\Implementation\StateContainer;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Structures\ListedObjectCollection;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

class ApplicationTest extends TestCase 
{
    public function testApplicationInDI(): void
    {
        $application = new Application();
        $this->assertSame($application, $this->container()->get(Application::class));
        $this->assertSame($application, Functions::nebula());
    }

    public function testCanAddProviders(): void
    {
        $providersContainer = $this->container()->get(ProvidersContainer::class);
        $providers = $providersContainer->getProviders();

        $this->assertInstanceOf(ListedObjectCollection::class, $providers);
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

        $this->container()->get(ProvidersContainer::class)->addProvider($provider);
    }

    /**
     * @throws AnotherInjectionExists
     */
    public function testRegisterStatesFromProvider(): void
    {
        $state = $this->getMockBuilder(State::class)->getMock();
        $state->expects($this->once())->method('notify');

        $provider = new class($state) extends Provider {
            public function __construct(private State $state) {}

            public function __invoke(StateContainer $stateContainer)
            {
                $stateContainer->add($this->state);
            }
        };

        $this->container()->get(ProvidersContainer::class)->addProvider($provider);
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

        $initListener = fn(Application $application, IEvent $event) => $this->assertInstanceOf(ApplicationInitEvent::class, $event);
        $completedListener = fn(IEvent $event) => $this->assertInstanceOf(ApplicationCompletedEvent::class, $event);

        $this->nebula->eInit->listen([$mock, 'callMe']);
        $this->nebula->eCompleted->listen([$mock, 'callMe']);

        $this->nebula->eInit->listen($initListener);
        $this->nebula->eCompleted->listen($completedListener);

        $this->nebula->run();
    }
}