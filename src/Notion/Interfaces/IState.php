<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

interface IState
{
    /**
     * Execute code when application get state 
     *
     * @return void
     */
    public function execute();
}