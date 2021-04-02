<?php

namespace Phox\Nebula\Atom\Notion\Abstracts;

use Phox\Nebula\Atom\Notion\Traits\TEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;

abstract class Event implements IEvent
{
    use TEvent;
}
