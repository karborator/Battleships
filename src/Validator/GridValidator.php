<?php

namespace Battleships\Validator;

class GridValidator implements ValidatorInterface
{
    private $errorMessage = [];
    private $grid;

    public function setData(array $grid): ValidatorInterface
    {
        $this->grid = $grid;

        return $this;
    }

    public function isValid($coordinates): bool
    {
        if (!isset($this->grid[$coordinates])) {
            $this->errorMessage[] = 'Invalid coordinates!';
            return false;
        }
        return true;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessage;
    }
}