<?php

namespace Battleships\Entity;

interface ShipEntityInterface
{
    public function getCount():int;
    public static function getChar(): string;
    public static function getSquares():int;
}