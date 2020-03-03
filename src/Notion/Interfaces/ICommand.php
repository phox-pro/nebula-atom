<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

interface ICommand
{
    /**
     * Run command in ConsoleState
     *
     * @return void
     */
    public function run();
}