<?php

namespace Phox\Nebula\Atom\Notion\Abstracts;

use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Traits\TEvent;

abstract class Event implements IEvent
{
    use TEvent;
}