<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Implementation\EventManager;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Implementation\StateContainer;
use Phox\Nebula\Atom\Implementation\States\InitState;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Notion\Interfaces\IEventManager;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;

class AtomProvider extends Provider
{
    private IDependencyInjection $dependencyInjection;

    /**
     * @throws StateExistsException
     */
    public function __invoke(IDependencyInjection $dependencyInjection, Application $application): void
    {
        $eventManager = $dependencyInjection->make(EventManager::class);
        $dependencyInjection->singleton($eventManager, IEventManager::class);

        $eventManager->initObjectEvents($application);
        $this->dependencyInjection = $dependencyInjection;

        $stateContainer = $this->dependencyInjection->make(StateContainer::class);

        $stateContainer->add(new InitState());
        $this->dependencyInjection->singleton($stateContainer, IStateContainer::class);

        $this->registerFallbacksListener($application);
    }

    private function registerFallbacksListener(Application $application): void
    {
        $application->eCompleted->listen(function (IStateContainer $container) {
            $containerFallbacks = $container->getFallbacks();

            foreach ($containerFallbacks as $fallbacks) {
                foreach ($fallbacks as $stateClass => $fallback) {
                    $this->dependencyInjection->call($fallback, [$stateClass]);
                }
            }
        });
    }
}