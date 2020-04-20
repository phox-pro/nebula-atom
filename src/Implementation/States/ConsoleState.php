<?php

namespace Phox\Nebula\Atom\Implementation\States;

use Phox\Nebula\Atom\Implementation\Console;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Traits\TEvent;

class ConsoleState extends State implements IEvent
{
    use TEvent;

    public function execute(Console $console)
    {
        call([$console, 'run']);
    }
}