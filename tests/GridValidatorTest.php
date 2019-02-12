<?php

namespace Tests;

use Battleships\Validator\GridValidator;
use PHPUnit\Framework\TestCase;

class GridValidatorTest extends TestCase
{
    public function testSetData()
    {
        $this->assertEquals(true, (new GridValidator())->setData(['test' => true])->isValid('test'));
        $this->assertEquals(false, (new GridValidator())->setData(['test' => true])->isValid('random'));
    }

    public function testSetDataReturnSelf()
    {
        $this->assertInstanceOf(GridValidator::class, (new GridValidator())->setData(['array']));
    }

    public function testIsValid()
    {
        $grid = ['test' => 'grid'];
        $this->assertEquals(true, (new GridValidator())->setData($grid)->isValid('test'));
        $this->assertEquals(false, (new GridValidator())->setData($grid)->isValid('random'));
    }

    public function testGetErrMessages()
    {
        $grid = ['test' => 'grid'];
        $gridValidator = new GridValidator();
        $gridValidator->setData($grid)->isValid('random');

        $this->assertNotEquals(0, count($gridValidator->getErrorMessages()));

        $gridValidator = new GridValidator();
        $gridValidator->setData($grid)->isValid('test');
        $this->assertEquals(0, count($gridValidator->getErrorMessages()));

    }
}