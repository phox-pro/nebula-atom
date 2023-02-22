<?php

namespace Phox\Nebula\Atom\Implementation\Services;

use Phox\Nebula\Atom\Notion\IServiceContainer;

trait ServiceContainerAccess
{
    protected function container(): IServiceContainer
    {
        return ServiceContainerFacade::instance();
    }
}