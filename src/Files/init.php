<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\ServiceContainer;

function init()
{
    $GLOBALS['dependencyInjection'] = new ServiceContainer; 
}