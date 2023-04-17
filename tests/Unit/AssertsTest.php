<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Implementation\AtomProvider;
use Phox\Nebula\Atom\Implementation\Event\Event;
use Phox\Nebula\Atom\Implementation\StartupConfiguration;
use Phox\Nebula\Atom\Implementation\State\State;
use Phox\Nebula\Atom\Notion\IStateContainer;
use Phox\Nebula\Atom\TestCase;
use PHPUnit\Framework\MockObject\Exception;

class AssertsTest extends TestCase
{
    public function testProviderExistsAssertion(): void
    {
        $app = new Application(new StartupConfiguration(
            registerProvidersFromPackages: false,
        ));

        $this->assertProviderNotExists(AtomProvider::class);

        $app->registerByConfig(require __DIR__ . '/../../nebula.php');

        $this->assertProviderExists(AtomProvider::class);
    }

    /**
     * @throws Exception
     */
    public function testStateExistsAssertion(): void
    {
        $app = new Application();
        $stateMock = $this->createMock(State::class);

        $this->assertStateNotExists($stateMock::class);

        $this->container()->get(IStateContainer::class)->add($stateMock);

        $this->assertStateExists($stateMock::class);
    }

    public function testEventFireAssertion(): void
    {
        $someEvent = new class extends Event {};

        $this->assertEventWillFire($someEvent::class);

        $someEvent->notify();
    }
}