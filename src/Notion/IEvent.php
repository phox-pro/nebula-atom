<?php

namespace Phox\Nebula\Atom\Notion;

use Phox\Structures\Interfaces\IEnumerableArray;

interface IEvent
{
    /**
     * @param callable(static): void $listener
     * @return void
     */
    public static function listen(callable $listener): void;

    /**
     * @return IEnumerableArray<callable(static): void>
     */
    public static function getListeners(): IEnumerableArray;

    public static function clearListeners(): void;

    public function notify(): void;
}