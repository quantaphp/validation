<?php

namespace Quanta\Validation\Types;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

final class PositiveInteger
{
    public function __construct(public readonly int $value)
    {
        if ($value < 0) {
            throw new InvalidDataException(Error::from(self::class, '{key} must be positive'));
        }
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
