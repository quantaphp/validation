<?php

namespace Quanta\Validation\Types;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

class StrictlyPositiveInteger extends AbstractInteger
{
    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new InvalidDataException(
                new Error(self::class, '{key} must be greater than 0, %s given', ['found' => $value])
            );
        }

        parent::__construct($value);
    }
}
