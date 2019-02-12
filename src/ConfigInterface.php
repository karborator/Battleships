<?php

namespace Battleships;

interface ConfigInterface
{
    public function get(string $key);
}