<?php

namespace Phox\Nebula\Atom\Implementation\States;

use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Traits\TEvent;

class DefineState extends State implements IEvent
{
    use TEvent;
}