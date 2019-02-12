<?php

namespace Battleships\Entity;

abstract class Ship implements ShipEntityInterface
{
    protected const SQUARES = 0;
    protected const CHAR = 'X';

    private $count = 0;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public static function getSquares():int
    {
        return static::SQUARES;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public static function getChar(): string
    {
        return static::CHAR;
    }

    public function __toString()
    {
        return static::getChar();
    }
}