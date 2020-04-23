<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\States\DefineState;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;

class AtomProvider extends Provider 
{
    public function define(IStateContainer $stateContainer)
    {
        $stateContainer->add(DefineState::class);
    }
}