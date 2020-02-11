<?php

use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;

if (!function_exists('container')) {
    /**
     * Get Service Container instance
     *
     * @return IDependencyInjection
     */
    function container() : IDependencyInjection
    {
        return $GLOBALS['dependencyInjection'];
    }
}