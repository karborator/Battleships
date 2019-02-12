<?php

namespace Tests;

use Battleships\Cache;
use Battleships\Entity\GridConfig;
use Battleships\Entity\Battleship;
use Battleships\Entity\Destroyer;
use Battleships\Entity\Ship;
use Battleships\Model\Grid;
use Battleships\Validator\GridValidator;
use PHPUnit\Framework\TestCase;

class GridModelTest extends TestCase
{
    public function testGetSettings()
    {
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn(1);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn(2);

        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        $grid->generateGrid();
        $this->assertEquals(
            [
                'width' => 10,
                'height' => 10,
                'numbers' => range(1, 10),
                'letters' => range('A', 'J'),
            ],
            $grid->getSettings()
        );

        $this->assertNotEquals(
            [
                'width' => 2,
                'height' => 2,
                'numbers' => range(1, 2),
                'letters' => range('A', 'B'),
            ],
            $grid->getSettings()
        );
    }

    public function testCountGeneratedShipSquaresOnGrid()
    {
        $battleshipsCount = 1;
        $destroyersCount = 2;
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn($battleshipsCount);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn($destroyersCount);

        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        $generatedGrid = $grid->generateGrid();

        $this->assertEquals($generatedGrid, $grid->generateGrid());

        $input = array_values($grid->generateGrid());
        $countGeneratedShips = 0;
        foreach ($input as $item) {
            if (Ship::getChar() === $item) {
                $countGeneratedShips++;
            }
        }

        $this->assertEquals(Battleship::getSquares() * $battleshipsCount + Destroyer::getSquares() * $destroyersCount,
            $countGeneratedShips);
    }

    public function testGenerateShip()
    {
        Cache::getInstance()->set('grid', null);
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn(0);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn(0);

        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        $generatedGrid = $grid->generateGrid();
        $generatedShipsSquaresCount = 0;
        foreach ($generatedGrid as $square) {
            if (Ship::getChar() === $square) {
                $generatedShipsSquaresCount++;
            }
        }
        $this->assertEquals(0, $generatedShipsSquaresCount);


        Cache::getInstance()->set('grid', null);
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn(1);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn(0);
        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        $gridWithShip = $grid->generateGrid();
        $generatedShipsSquaresCount = 0;
        foreach ($gridWithShip as $square) {
            if (BattleShip::getChar() === $square) {
                $generatedShipsSquaresCount++;
            }
        }

        $this->assertEquals(Battleship::getSquares(), $generatedShipsSquaresCount);
    }

    public function testShoot()
    {
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn(1);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn(2);


        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        $generatedGrid = $grid->generateGrid();

        foreach ($generatedGrid as $square => $value) {
            if (Ship::getChar() === $value) {
                $coordinatesToShoot = $square;
                $coordinatesHit = Cache::getInstance()->get('coordinatesHit');
                unset($coordinatesHit[$coordinatesToShoot]);
                Cache::getInstance()->set('coordinatesHit', $coordinatesHit);
                continue;
            }
            $coordinatesToShootAndMiss = $square;
        }

        if ($coordinatesToShoot) {
            $grid->shoot($coordinatesToShoot);
            $this->assertEquals(true, $grid->getCoordinatesHit()[$coordinatesToShoot] ?? null);
        }

        if ($coordinatesToShootAndMiss) {
            $grid->shoot($coordinatesToShootAndMiss);
            $this->assertEquals(true, $grid->getCoordinatesMiss()[$coordinatesToShootAndMiss] ?? null);
        }
    }

    public function testGetPlayAgainDefaultValue()
    {
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn(1);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn(2);


        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        Cache::getInstance()->set('playAgain', false);
        $this->assertEquals(false, $grid->getPlayAgain());
    }

    public function testGetCoordinatesHitDefaultValue()
    {
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn(1);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn(2);


        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        Cache::getInstance()->set('coordinatesHit', null);
        $this->assertEquals([], $grid->getCoordinatesHit());
    }


    public function testGetCoordinatesMissDefaultValue()
    {
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn(1);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn(2);

        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        Cache::getInstance()->delete('coordinatesMiss');
        $this->assertEquals([], $grid->getCoordinatesMiss());
    }

    public function testGetValidationMessages()
    {
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn(1);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn(2);

        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        $grid->setValidationMessages('Test');
        $this->assertEquals(['messages' => ['Test']], $grid->getValidationMessages());
    }

    public function testGetShootMessages()
    {
        $configMock = $this->getMockBuilder(GridConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('getGridWidth')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getGridHeight')
            ->willReturn(10);
        $configMock->expects($this->any())
            ->method('getBattleshipsCount')
            ->willReturn(1);
        $configMock->expects($this->any())
            ->method('getDestroyersCount')
            ->willReturn(2);

        $grid = new Grid($configMock, Cache::getInstance(), new GridValidator());
        $this->assertEquals([], $grid->getShootMessages());
    }
}