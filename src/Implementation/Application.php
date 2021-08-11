<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\AtomProvider;
use Phox\Nebula\Atom\Implementation\Events\ApplicationInitEvent;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Structures\ObjectCollection;

class Application 
{
    public const GLOBALS_KEY = 'nebulaApplicationInstance';

    public IDependencyInjection $dependencyInjection;
    public ApplicationInitEvent $eInit;

    /**
     * @var ObjectCollection<Provider>
     */
    protected ObjectCollection $providers;

    /**
     * @throws Exceptions\AnotherInjectionExists
     */
    public function __construct()
	{
	    $this->dependencyInjection = new ServiceContainer();
	    $this->dependencyInjection->singleton($this);
	    $this->dependencyInjection->singleton(new StateContainer(), IStateContainer::class);

        $this->providers = new ObjectCollection(Provider::class);
        $this->addProvider(new AtomProvider());

        $GLOBALS[static::GLOBALS_KEY] = fn(): ?Application => $this->dependencyInjection->get(self::class);
    }

    /**
     * Get all application providers
     *
     * @return ObjectCollection<Provider>
     */
    public function getProviders() : ObjectCollection
    {
        return $this->providers;
    }

    /**
     * Add provider to application
     *
     * @param Provider $provider
     * @return void
     */
    public function addProvider(Provider $provider): void
    {
        $this->providers->set(get_class($provider), $provider);

        if (is_callable($provider)) {
            $this->dependencyInjection->call($provider);
        }
    }

    /**
     * Run Nebula application
     *
     * @return void
     * @throws Exceptions\AnotherInjectionExists
     */
    public function run(): void
    {
       $this->enrichment(); 
    }

    /**
     * @throws Exceptions\AnotherInjectionExists
     */
    protected function enrichment(): void
    {
        /** @var IStateContainer $stateContainer */
        $stateContainer = $this->dependencyInjection->get(IStateContainer::class);
        $root = $stateContainer->getRoot();

        foreach ($root as $state) {
            $state->setPrevious($previous ?? null);
            $this->callState($state);

            $previous = $state;
        }
    }

    /**
     * @throws Exceptions\AnotherInjectionExists
     */
    protected function callState(State $state)
    {
        /** @var IStateContainer $stateContainer */
        $stateContainer = $this->dependencyInjection->get(IStateContainer::class);
        $this->dependencyInjection->singleton($state, State::class);

        $state->notify();

        $children = $stateContainer->getChildren($state::class);

        foreach ($children as $child) {
            $child->setPrevious($previous ?? null);
            $this->callState($child);

            $previous = $child;
        }
    }
}