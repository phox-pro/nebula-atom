<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\States\ConsoleState;
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

	public function __construct()
	{
        $this->providers = new Collection(Provider::class);
        get(IStateContainer::class)->add(DefineState::class);
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
        !is_callable([$provider, 'define']) ?: call([$provider, 'define']);
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

    /**
     * Run Nebula application as CLI
     *
     * @return void
     */
    public function runConsole(array $argv)
    {
        get(IStateContainer::class)->addAfter(ConsoleState::class, DefineState::class);
        container()->singleton(new Console($argv));
        $this->enrichment();
    }

    protected function enrichment()
    {
        $root = get(IStateContainer::class)->getRoot();
        foreach ($root as $state) {
            $this->callState($state);
        }
    }

    protected function callState(string $stateClass)
    {
        $state = make($stateClass);
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