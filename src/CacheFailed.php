<?php

namespace Battleships;

use Psr\SimpleCache\CacheException;

class CacheFailed implements CacheException
{
    const METHOD_NOT_IMPLEMENTED = 'Method not implemented';

    const ARGUMENT_USAGE_NOT_IMPLEMENTED = 'Argument %s usage not implemented';

    public static function argumentNotImplemented(string $argName)
    {
        return new self(sprintf(self::ARGUMENT_USAGE_NOT_IMPLEMENTED, $argName));
    }

    public static function methodNotImplemented()
    {
        return new self(self::METHOD_NOT_IMPLEMENTED);
    }
}