<?php

namespace Phox\Nebula\Atom\Implementation;

use LogicException;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;

class Functions
{
    public static function nebula(): Application
    {
        if (!array_key_exists(Application::GLOBALS_KEY, $GLOBALS)) {
            new Application();
        }

        /** @var callable $callback */
        $callback = $GLOBALS[Application::GLOBALS_KEY];

        return $callback();
    }

    public static function container(): IDependencyInjection
    {
        return static::nebula()->dependencyInjection->get(IDependencyInjection::class) ?? throw new LogicException();
    }
}