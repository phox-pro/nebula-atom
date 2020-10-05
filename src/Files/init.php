<?php

namespace Phox\Nebula\Atom\Files;

use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Implementation\Console;
use Phox\Nebula\Atom\Implementation\ExceptionHandler;
use Phox\Nebula\Atom\Implementation\ServiceContainer;
use Phox\Nebula\Atom\Implementation\StateContainer;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Throwable;

function init()
{
    $GLOBALS['dependencyInjection'] = new ServiceContainer;
    container()->singleton(StateContainer::class, IStateContainer::class);
    container()->singleton(Application::class);
    container()->singleton(Console::class);
    container()->singleton(ExceptionHandler::class);
    set_exception_handler(fn (Throwable $throwable) => get(ExceptionHandler::class)->execute($throwable));
}