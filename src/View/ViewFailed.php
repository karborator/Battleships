<?php

namespace Battleships\View;

class ViewFailed extends \Exception
{
    const BAD_TEMPLATE = 'Template not found or not readable!';

    public static function templateNotFound()
    {
        return new static(static::BAD_TEMPLATE);
    }
}