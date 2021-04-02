<?php

namespace Phox\Nebula\Atom\Implementation\States;

use Phox\Nebula\Atom\Notion\Traits\TEvent;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;

class DefineState extends State implements IEvent
{
    use TEvent;
}
