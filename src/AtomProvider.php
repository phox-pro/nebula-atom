<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\StateContainer;
use Phox\Nebula\Atom\Implementation\States\InitState;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;

class AtomProvider extends Provider
{
    public function __invoke(StateContainer $stateContainer)
    {
        $stateContainer->add(new InitState());
    }
}