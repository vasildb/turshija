<?php

namespace Vasil\Turshija\Helpers;

class App
{
    public static function root(): string
    {
        $dir = __DIR__;

        $pos = strrpos($dir, '/src/Helpers');

        return substr($dir, 0, $pos);
    }

    public static function webDir(): string
    {
        return self::root() . '/web';
    }
}
