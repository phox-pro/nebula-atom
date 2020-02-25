<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\States\DefineState;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;

class Application 
{
    /**
     * Providers collection
     */
    protected Collection $providers;

    protected State $currentState;

	public function __construct()
	{
        $this->providers = new Collection(Provider::class);
        $this->currentState = make(DefineState::class);
    }
    
    /**
     * Get all application providers
     *
     * @return Provider[]|Collection
     */
    public function getProviders() : Collection
    {
        return $this->providers;
    }

    /**
     * Add provider to application
     *
     * @param Provider $provider
     * @return void
     */
    public function addProvider(Provider $provider)
    {
        $this->providers->set(get_class($provider), $provider);
    }

    /**
     * Run Nebula application
     *
     * @return void
     */
    public function run()
    {
       $this->enrichment(); 
    }

    protected function enrichment()
    {
        foreach ($this->providers as $provider) {
            !is_callable([$provider, 'define']) ?: call([$provider, 'define']);
        }
        $root = get(IStateContainer::class)->getRoot();
        foreach ($root as $state) {
            $this->callState($state);
        }
    }

    protected function callState(string $stateClass)
    {
        $state = make($stateClass);
        $this->currentState = $state;
        container()->singleton($state, State::class);
        if ($state instanceof IEvent) {
            $state::notify();
        }
        !is_callable([$state, 'execute']) ?: call([$state, 'execute']);
        $children = get(IStateContainer::class)->getChildren($stateClass);
        foreach ($children as $child) {
            $this->callState($child);
        }
    }
}