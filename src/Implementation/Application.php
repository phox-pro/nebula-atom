<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\AtomProvider;
use Phox\Nebula\Atom\Implementation\Events\ApplicationCompletedEvent;
use Phox\Nebula\Atom\Implementation\Events\ApplicationInitEvent;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;

class Application 
{
    public const GLOBALS_KEY = 'nebulaApplicationInstance';

    public IDependencyInjection $dependencyInjection;

    // Events
    public ApplicationInitEvent $eInit;
    public ApplicationCompletedEvent $eCompleted;

    /**
     * @throws Exceptions\AnotherInjectionExists
     */
    public function __construct()
	{
        $GLOBALS[static::GLOBALS_KEY] = fn(): ?Application => $this->dependencyInjection->get(self::class);
        $this->initEvents();

	    $this->dependencyInjection = new ServiceContainer();

	    $this->dependencyInjection->singleton($this);
	    $this->dependencyInjection->singleton(new StateContainer());

        $providers = new ProvidersContainer();
        $providers->addProvider(new AtomProvider());

        $this->dependencyInjection->singleton($providers);
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
        $this->eInit->notify();

        $stateContainer = $this->dependencyInjection->get(StateContainer::class);
        $root = $stateContainer->getRoot();

        foreach ($root as $state) {
            $state->setPrevious($previous ?? null);
            $this->callState($state);

            $previous = $state;
        }

        $this->eCompleted->notify();
    }

    /**
     * @throws Exceptions\AnotherInjectionExists
     */
    protected function callState(State $state)
    {
        $stateContainer = $this->dependencyInjection->get(StateContainer::class);
        $this->dependencyInjection->singleton($state, State::class);

        $state->notify();

        $children = $stateContainer->getChildren($state::class);

        foreach ($children as $child) {
            $child->setPrevious($previous ?? null);
            $this->callState($child);

            $previous = $child;
        }
    }

    protected function initEvents(): void
    {
        $this->eInit = new ApplicationInitEvent();
        $this->eCompleted = new ApplicationCompletedEvent();
    }
}