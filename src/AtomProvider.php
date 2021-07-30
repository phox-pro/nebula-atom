<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\States\InitState;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;

class AtomProvider extends Provider
{
    public function __invoke(IStateContainer $stateContainer)
    {
        $stateContainer->add(new InitState());
    }
}