<?php

namespace Battleships\Entity;

use Battleships\Config;
use Battleships\ConfigInterface;

class GridConfig implements GridConfigEntityInterface
{
    private static $instance;
    private $config;

    private function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public static function getInstance(): GridConfigEntityInterface
    {
        return self::$instance ?? self::$instance = new static(Config::getInstance());
    }

    public function getGridWidth(): int
    {
        return (int)$this->get('gridWidth');
    }

    public function getGridHeight(): int
    {
        return (int)$this->get('gridHeight');
    }

    public function getBattleshipsCount(): int
    {
        return (int)$this->get('battleshipsCount');
    }

    public function getDestroyersCount(): int
    {
        return (int)$this->get('destroyersCount');
    }

    public function get(string $key)
    {
        return $this->config->get($key);
    }
}