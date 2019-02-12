<?php

namespace Battleships\Validator;

interface ValidatorInterface
{
    public function setData(array $grid): self;

    public function isValid($data): bool;

    public function getErrorMessages(): array;
}