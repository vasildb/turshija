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

    public static function contentsDir(): string
    {
        $opts = getopt('', ['from:']);
        return $opts['from'];
    }

    public static function exportDir(): string
    {
        $opts = getopt('', ['to:']);
        return $opts['to'];
    }
}
