<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\Notion\Traits\TEvent;
use stdClass;

class ApplicationTest extends TestCase 
{
    /**
     * @test
     */
    public function coreTest()
    {
        $app = get(Application::class);
        $this->assertInstanceOf(Application::class, $app);
        $this->assertSame($app, app());
    }

    /**
     * @test
     */
    public function addProvidersTest()
    {
        $providers = app()->getProviders();
        $this->assertInstanceOf(Collection::class, $providers);
        $this->assertEquals(1, $providers->count());
        $provider = new class extends Provider {};
        app()->addProvider($provider);
        $this->assertArrayHasKey(get_class($provider), $providers);
    }

    /**
     * @test
     */
    public function runApplicationTest()
    {
        /**
         * @var Provider|\PHPUnit\Framework\MockObject\MockObject $provider
         */
        $provider = $this->getMockBuilder(Provider::class)->addMethods(['define'])->getMock();
        $provider->expects($this->once())->method('define');
        app()->addProvider($provider);
        app()->run();
    }

    /**
     * @test
     */
    public function registerLogicTest()
    {
        container()->singleton(new class extends stdClass {
            public bool $checked = false; 
        }, stdClass::class);
        call(fn (stdClass $obj) => $this->assertFalse($obj->checked));
        $provider = new class extends Provider {
            public function define(stdClass $object)
            {
                $object->checked = true;
            }
        };
        app()->addProvider($provider);
        app()->run();
        $this->assertTrue(get(stdClass::class)->checked);
    }

    /**
     * @test
     */
    public function registerStateFromProvider()
    {
        $provider = new class($this) extends Provider {
            private ApplicationTest $case;

            public function __construct(ApplicationTest $case)
            {
                $this->case = $case; 
            }

            public function define(IStateContainer $states) {
                $stateClass = get_class(new class extends State implements IEvent {
                    use TEvent;
                });
                $stateClass::listen(fn (State $state) => $this->case->assertInstanceOf($stateClass, $state));
                $states->add($stateClass);
            }
        };
        app()->addProvider($provider);
        app()->run();
    }
}