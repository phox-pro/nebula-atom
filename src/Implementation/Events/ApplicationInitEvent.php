<?php

namespace Phox\Nebula\Atom\Implementation\Events;

use Phox\Nebula\Atom\Notion\Abstracts\Event;

/**
 * @template T as callable(Application)
 * @psalm-method void listen(T $listener)
 */
class ApplicationInitEvent extends Event
{

}