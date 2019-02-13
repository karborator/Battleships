<?php

namespace Battleships;

class ConfigFailed extends \RuntimeException
{
    const BAD_DIRECTORY = 'Config error, bad directory given.';

    public static function badDirectory()
    {
        return new static(static::BAD_DIRECTORY);
    }
}