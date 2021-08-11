<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\AtomProvider;
use Phox\Nebula\Atom\Implementation\Exceptions\AnotherInjectionExists;
use Phox\Nebula\Atom\Implementation\Functions;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Structures\ObjectCollection;
use PHPUnit\Framework\MockObject\MockObject;

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
        $providers = $this->nebula->getProviders();

        $this->assertInstanceOf(ObjectCollection::class, $providers);
        $this->assertEquals(1, $providers->count());
        $this->assertInstanceOf(AtomProvider::class, $providers->first());

        $provider = new class extends Provider {};
        $this->nebula->addProvider($provider);

        $this->assertArrayHasKey(get_class($provider), $providers);
    }

    public function testApplicationCallProvider(): void
    {
        /** @var Provider|MockObject $provider */
        $provider = $this->getMockBuilder(Provider::class)->addMethods(['__invoke'])->getMock();
        $provider->expects($this->once())->method('__invoke');

        $this->nebula->addProvider($provider);
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

            public function __invoke(IStateContainer $stateContainer)
            {
                $stateContainer->add($this->state);
            }
        };

        $this->nebula->addProvider($provider);
        $this->nebula->run();
    }
}