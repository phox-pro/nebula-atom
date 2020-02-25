<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Implementation\ServiceContainer;
use Phox\Nebula\Atom\Implementation\StateContainer;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;

function init()
{
    $GLOBALS['dependencyInjection'] = new ServiceContainer;
    container()->singleton(make(StateContainer::class), IStateContainer::class);
    container()->singleton(make(Application::class));
}