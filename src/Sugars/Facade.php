<?php

namespace Lxj\ClosurePHP\Sugars;

use function Lxj\ClosurePHP\Sugars\Object\callObjectMethod;

abstract class Facade
{
    protected static function getAccessor()
    {
        return null;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|null
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
        $accessor = static::getAccessor();
        if ($accessor) {
            return callObjectMethod(
                $accessor,
                $name,
                Scope::PUBLIC,
                $arguments
            );
        }

        return null;
    }
}
