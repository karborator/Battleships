<?php

namespace Tests;

use Battleships\Entity\Battleship;
use Battleships\Entity\Destroyer;
use Battleships\Entity\ShipEntityInterface;

class ShipTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $this->assertInstanceOf(Battleship::class, new Battleship(1));
        $this->assertInstanceOf(Destroyer::class, new Destroyer(1));
        $this->assertInstanceOf(ShipEntityInterface::class, new Destroyer(1));
    }

    public function testToString()
    {
        $this->assertEquals(true, is_string((string)new Battleship(1)));
        $this->assertEquals(true, is_string((string)new Destroyer(1)));
    }
}