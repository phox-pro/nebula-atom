<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Implementation\Exceptions\MustImplementInterface;

interface IStateContainer
{
    /**
     * Get all States
     *
     * @return IState[]|Collection
     */
    public function getAll() : Collection;

    /**
     * Get root States
     *
     * @return IState[]|Collection
     */
    public function getRoot() : Collection;

    /**
     * Get state children
     *
     * @param string $parentClass
     * @return IState[]|Collection
     */
    public function getChildren(string $parentClass) : Collection;

    /**
     * Add State to container
     *
     * @param string $stateClass
     * 
     * @throws MustImplementInterface
     * @throws StateExistsException
     * 
     * @return void
     */
    public function add(string $stateClass);

    /**
     * Add State to container and set as child
     *
     * @param string $stateClass
     * @param string $parentClass
     * 
     * @throws MustImplementInterface
     * @throws StateExistsException
     * 
     * @return void
     */
    public function addAfter(string $stateClass, string $parentClass);
}