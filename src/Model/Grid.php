<?php

namespace Battleships\Model;

use Battleships\Cache;
use Battleships\Entity\Battleship;
use Battleships\Entity\Destroyer;
use Battleships\Entity\GridConfig;
use Battleships\Entity\GridConfigEntityInterface;
use Battleships\Entity\Ship;
use Battleships\Entity\ShipEntityInterface;
use Battleships\Validator\GridValidator;
use Battleships\Validator\ValidatorInterface;
use Psr\SimpleCache\CacheInterface;

class Grid implements GridModelInterface
{
    private $gridValidator;
    private $validationMessages = [];
    private $shootMessages = [];
    private $gridWidth;
    private $gridHeight;
    private $gridNumbers = [];
    private $gridLetters = [];
    private $battleshipsCount;
    private $destroyersCount;
    private $cache;

    public function __construct(
        GridConfigEntityInterface $config,
        CacheInterface $cache,
        ValidatorInterface $gridValidator
    ) {
        $this->gridValidator = $gridValidator;
        $this->gridWidth = $config->getGridWidth();
        $this->gridHeight = $config->getGridHeight();
        $this->battleshipsCount = $config->getBattleshipsCount();
        $this->destroyersCount = $config->getDestroyersCount();
        $this->cache = $cache;

        $this->gridNumbers = range(1, $this->gridWidth);
        $this->gridLetters = array_slice(range('A', 'Z'), 0, $this->gridHeight);

    }

    public static function factory(): GridModelInterface
    {
        $model = new self(GridConfig::getInstance(), Cache::getInstance(), new GridValidator());
        $model->generateGrid();
        return $model;
    }

    public function getSettings(): array
    {
        return [
            'width' => $this->gridWidth,
            'height' => $this->gridHeight,
            'numbers' => $this->gridNumbers,
            'letters' => $this->gridLetters,
        ];
    }

    public function generateGrid(): array
    {
        if ($this->cache->has('grid')) {
            return $this->cache->get('grid');
        }

        $width = $this->gridWidth;
        $height = $this->gridHeight;
        $grid = [];
        //create the grid with coordinates
        for ($row = 0; $row <= $width; $row++) {
            for ($col = 0; $col <= $height; $col++) {
                if (!isset($this->gridNumbers[$row]) || !isset($this->gridLetters[$col])) {
                    continue;
                }

                if (!isset($grid[$this->gridLetters[$col] . $this->gridNumbers[$row]])) {
                    $grid[$this->gridLetters[$col] . $this->gridNumbers[$row]] = true;
                }
            }
        }

        $grid = $this->placeShipsOnGrid(
            $grid,
            $this->gridLetters[rand(0, $height - 1)],
            $this->gridNumbers[rand(0, $width - 1)],
            ...$this->getShipsToGenerate()
        );

        $this->cache->set('grid', $grid);
        return $grid;
    }

    public function shoot(string $coordinates)
    {
        if (!$this->handlePreShoot($coordinates)) {
            return;
        }

        //Shoot
        $grid = $this->generateGrid();
        if (Ship::getChar() === $grid[$coordinates]) {
            $this->shootMessages['messages'] = [self::SHOOT_MSG_HIT];
            $coordinatesHit = $this->cache->get('coordinatesHit', []);
            $coordinatesHit[$coordinates] = true;
            $this->cache->set("coordinatesHit", $coordinatesHit);
            $this->setSunk($coordinates);
        } else {
            $this->shootMessages['messages'] = [self::SHOOT_MSG_MISS];
            $coordinatesMiss = $this->cache->get('coordinatesMiss', []);
            $coordinatesMiss[$coordinates] = true;
            $this->cache->set('coordinatesMiss', $coordinatesMiss);
        }

        $this->handlePostShoot($coordinates);
    }

    public function getPlayAgain(): bool
    {
        return $this->cache->get('playAgain', false);
    }

    public function getCoordinatesHit(): array
    {
        return $this->cache->get('coordinatesHit', []);
    }

    public function getCoordinatesMiss(): array
    {
        return $this->cache->get('coordinatesMiss', []);
    }

    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }

    public function setValidationMessages(string $message)
    {
        $this->validationMessages['messages'][] = $message;
    }

    public function getShootMessages(): array
    {
        return $this->shootMessages;
    }

    private function calculateTotalHits(): int
    {
        return count($this->getCoordinatesHit()) + count($this->getCoordinatesMiss());
    }

    private function getRandomInt(int $number): int
    {
        $number = $number < 1 ? rand(1, $this->gridWidth) : $number;
        $newRand = random_int(1, $number);
        if ($newRand === $number) {
            $newRand = $number + 1;
        }
        return $newRand;
    }

    private function getSunk(): array
    {
        return $this->cache->get('sunk', []);
    }

    private function cleanSunk()
    {
        $this->cache->delete('sunk');
        return;
    }

    private function setSunk($coordinates): self
    {
        $sunk = $this->cache->get('sunk', []);
        $sunk[] = $coordinates;
        $this->cache->set('sunk', $sunk);
        return $this;
    }

    private function getShipsToGenerate(): array
    {
        return [
            new Battleship($this->battleshipsCount),
            new Destroyer($this->destroyersCount),
        ];
    }

    private function getShipSunkAndTotalSquaresToWin(string $coordinates): array
    {
        list($alpha, $numeric) = sscanf($coordinates, "%[A-Z]%d");
        $alpha++;
        $nexPositionValue = $this->generateGrid()[$alpha . $numeric] ?? null;
        $countSunk = count($this->getSunk());
        $totalNeededToWin = null;
        $shipsToGenerate = $this->getShipsToGenerate();
        $noCharOnNextPosition = $matchSquares = false;
        /** @var ShipEntityInterface $ship */
        foreach ($shipsToGenerate as $ship) {
            $squares = $ship::getSquares();
            $matchSquares = ($squares === $countSunk) ? true : $matchSquares;
            $noCharOnNextPosition = ($ship::getChar() !== $nexPositionValue) ? true : $noCharOnNextPosition;
            $totalNeededToWin += $squares * $ship->getCount();
        }

        $shootMsgSunk = ($matchSquares && $noCharOnNextPosition);

        return [$shootMsgSunk, $totalNeededToWin];
    }


    private function placeShipsOnGrid(array $grid, string $randomLetter, int $randomNumber, Ship...$ships): array
    {
        foreach ($ships as $ship) {
            $countToGenerate = $ship->getCount();
            while ($countToGenerate > 0) {
                $grid = $this->generateShip(clone $ship, $grid, $randomLetter, $randomNumber);
                $countToGenerate--;
            }
        }

        return $grid;
    }

    private function generateShip(ShipEntityInterface $ship, array $grid, string $letter, int $number): array
    {
        $gridCopy = $grid;
        $counter = $ship::getSquares();
        for ($i = 1; $i <= $counter; $i++) {
            $result = $this->handleCoordinatesOutOfGrid($grid, $gridCopy, $ship, $letter, $number);
            if ($result) {
                return $result;
            }

            if ($this->handleTakenPosition($ship, $gridCopy, $letter, $number, $counter)) {
                continue;
            }

            if (!isset($onePositionBack)) {
                $result = $this->handlePositionAroundShip($gridCopy, $grid, $ship, $letter, $number, $onePositionBack);
                if ($result) {
                    return $result;
                }
            }


            $gridCopy[$letter . $number] = (string)$ship;
            $letter++;
        }

        return $gridCopy;
    }

    private function handlePreShoot(string $coordinates): bool
    {
        //Commands
        if (self::COMMAND_SHOW === strtolower($coordinates)) {
            $this->shootMessages['show'] = true;
            return false;
        }

        //Validation
        if (!$this->gridValidator->setData($this->generateGrid())->isValid($coordinates)) {
            $this->setValidationMessages(implode(PHP_EOL, $this->gridValidator->getErrorMessages()));
            return false;
        }

        return true;
    }

    private function handlePostShoot(string $coordinates)
    {
        $totalSquaresToWin = $this->handleShipSunk($coordinates);
        $this->handlePlayerWin($totalSquaresToWin);
    }

    private function handleShipSunk(string $coordinates): int
    {
        list($shipSunk, $totalSquaresToWin) = $this->getShipSunkAndTotalSquaresToWin($coordinates);
        if ($shipSunk) {
            $this->shootMessages['messages'] = [self::SHOOT_MSG_SUNK];
            $this->cleanSunk();
        }
        return $totalSquaresToWin;
    }

    private function handlePlayerWin(int $totalSquaresToWin): void
    {
        if ((null !== $totalSquaresToWin) && ($totalSquaresToWin === count($this->getCoordinatesHit()))) {
            $this->shootMessages['messages'] = [sprintf(self::SHOOT_MSG_WELL_DONE, $this->calculateTotalHits())];

            $this->cache->clear();
            $this->cache->set('playAgain', true);
        }
    }

    private function handleCoordinatesOutOfGrid(
        array $grid,
        array $gridCopy,
        ShipEntityInterface $ship,
        string $letter,
        int $number
    ) {
        if (isset($gridCopy[$letter . $number])) {
            return;
        }

        $letter = chr(ord($letter) - $ship::getSquares());
        if (isset($grid[$letter . $number])) {
            return $this->generateShip($ship, $grid, $letter, $number);
        }

        $letter = chr(ord($letter) + $ship::getSquares());
        if (isset($grid[$letter . $number])) {
            return $this->generateShip($ship, $grid, $letter, $number);
        }

        $letter = chr(ord($letter) + $this->getRandomInt(0));
        $number--;
        return $this->generateShip($ship, $grid, $letter, $number);
    }

    private function handlePositionAroundShip(
        array $gridCopy,
        array $grid,
        ShipEntityInterface $ship,
        string $letter,
        int $number,
        &$onePositionBack
    ) {
        $onePositionBack = $gridCopy[chr(ord($letter) - 1) . $number] ?? null;
        $nextPositionAfterShip = $gridCopy[chr(ord($letter) + $ship::getSquares()) . $number] ?? null;
        if (($onePositionBack && $ship::getChar() === $onePositionBack)
            || ($nextPositionAfterShip && $ship::getChar() === $nextPositionAfterShip)
        ) {
            $letter++;
            $number++;
            return $this->generateShip($ship, $grid, $letter, $number);
        }
    }

    private function handleTakenPosition(
        ShipEntityInterface $ship,
        array $gridCopy,
        string &$letter,
        int &$number,
        int &$counter
    ) {
        if ($ship::getChar() === $gridCopy[$letter . $number]) {
            $letter++;
            $number++;
            $counter++;
            return true;
        }

        return false;
    }
}