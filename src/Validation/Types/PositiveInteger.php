<?php

namespace Quanta\Validation\Types;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

class PositiveInteger extends AbstractInteger
{
    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidDataException(
                Error::from('{key} must be positive, %s given', ['value' => $value], self::class),
            );
        }

        parent::__construct($value);
    }
}
