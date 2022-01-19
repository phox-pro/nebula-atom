<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\AtomProvider;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Notion\Interfaces\IEventManager;
use Phox\Nebula\Atom\Notion\Interfaces\IProvidersContainer;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;

class Application 
{
    protected IDependencyInjection $dependencyInjection;

    // Events
    public BasicEvent $eInit;
    public BasicEvent $eCompleted;

    /**
     * @throws Exceptions\AnotherInjectionExists
     */
    public function __construct()
	{
        $this->initDIContainer();

        $providers = $this->dependencyInjection->make(ProvidersContainer::class);
        $providers->addProvider(new AtomProvider());

        $this->dependencyInjection->singleton($providers, IProvidersContainer::class);
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

    public function getDIContainer(): IDependencyInjection
    {
        return $this->dependencyInjection->get(IDependencyInjection::class);
    }

    /**
     * @throws Exceptions\AnotherInjectionExists
     */
    protected function enrichment(): void
    {
        $eventManager = $this->getDIContainer()->get(IEventManager::class);
        $eventManager->notify($this->eInit);
        $dependencyInjection = $this->dependencyInjection->get(IDependencyInjection::class);

        $stateContainer = $dependencyInjection->get(IStateContainer::class);
        $root = $stateContainer->getRoot();

        foreach ($root as $state) {
            $state->setPrevious($previous ?? null);
            $this->callState($state);

            $previous = $state;
        }

        $eventManager->notify($this->eCompleted);
    }

    /**
     * @throws Exceptions\AnotherInjectionExists
     */
    protected function callState(State $state): void
    {
        $dependencyInjection = $this->getDIContainer();
        $eventManager = $dependencyInjection->get(IEventManager::class);

        $stateContainer = $dependencyInjection->get(IStateContainer::class);
        $dependencyInjection->singleton($state, State::class);

        $eventManager->notify($state);

        $children = $stateContainer->getChildren($state::class);

        foreach ($children as $child) {
            $child->setPrevious($previous ?? null);
            $this->callState($child);

            $previous = $child;
        }
    }

    /**
     * @throws Exceptions\AnotherInjectionExists
     */
    protected function initDIContainer(): void
    {
        $this->dependencyInjection = new ServiceContainer();
        $this->dependencyInjection->singleton($this);
    }
}