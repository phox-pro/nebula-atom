<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Implementation\Events\ApplicationCompletedEvent;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerFacade;
use Phox\Nebula\Atom\Notion\IProvider;
use Phox\Nebula\Atom\Notion\IStateContainer;

class AtomProvider implements IProvider
{
    public function register(): void
    {
        $states = ServiceContainerFacade::get(IStateContainer::class);
        $states->add(new InitState());
    }
}