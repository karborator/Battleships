<?php

namespace Battleships\Entity;

use Battleships\ConfigInterface;

interface GridConfigEntityInterface extends ConfigInterface
{
    public function getGridWidth(): int;
    public function getGridHeight(): int;
    public function getBattleshipsCount(): int;
    public function getDestroyersCount(): int;
}