<?php

namespace Tests;

use Battleships\Config;
use Battleships\Controller\GridController;
use Battleships\Request;
use Battleships\View\ViewInterface;
use PHPUnit\Framework\TestCase;

class GridControllerTest extends TestCase
{
    public function testIndexGet()
    {
        $configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->any())
            ->method('get')
            ->willReturn(null);

        $rqMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $rqMock->expects($this->any())
            ->method('isPost')
            ->willReturn(false);

        $this->assertInstanceOf(ViewInterface::class, GridController::factory($configMock)->index($rqMock));

        $rqMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $rqMock->expects($this->any())
            ->method('isPost')
            ->willReturn(true);
        $rqMock->expects($this->any())
            ->method('getPost')
            ->willReturn(['coord' => 'A5']);

        $this->assertInstanceOf(ViewInterface::class, GridController::factory($configMock)->index($rqMock));
    }
}