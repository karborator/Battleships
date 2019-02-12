<?php

namespace Battleships\View;

interface ViewInterface
{
    public function boot(string $template, array $data = []);

    public function __toString();
}