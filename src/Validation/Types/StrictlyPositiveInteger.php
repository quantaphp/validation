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
                Error::from('{key} must be greater than 0, %s given', ['value' => $value], self::class),
            );
        }

        parent::__construct($value);
    }
}
