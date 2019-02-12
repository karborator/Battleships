<?php

namespace Battleships\Model;


use Battleships\Entity\ShipEntityInterface;

interface GridModelInterface
{
    const GRID_HEIGHT = 10;
    const GRID_WIDTH = 10;
    const BATTLESHIPS_COUNT = 1;
    const DESTROYERS_COUNT = 2;
    const SHOOT_MSG_MISS = '***MISS***';
    const SHOOT_MSG_HIT = '***HIT***';
    const SHOOT_MSG_SUNK = '*** Sunk ***';
    const SHOOT_MSG_WELL_DONE = "Well done! You completed the game in %s shots";
    const COMMAND_SHOW = 'show';

    public function getSettings(): array;

    public function generateGrid(): array;

    public function shoot(string $coordinates);

    public function getPlayAgain(): bool;

    public function getCoordinatesHit(): array;

    public function getCoordinatesMiss(): array;

    public function getValidationMessages(): array;

    public function setValidationMessages(string $message);

    public function getShootMessages(): array;
}