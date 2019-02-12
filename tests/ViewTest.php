<?php

namespace Tests;

use Battleships\View\View;

class ViewTest extends \PHPUnit\Framework\TestCase
{
    public function testBoot()
    {
        $view = new View(View::VIEW_TYPE, View::VIEW_PATH);
        $this->assertInstanceOf(View::class, $view->boot('grid'));
    }
}