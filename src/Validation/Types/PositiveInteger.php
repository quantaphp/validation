<?php

namespace Quanta\Validation\Types;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

final class PositiveInteger
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidDataException(
                new Error(self::class, '{key} must be positive, %s given', ['found' => $value])
            );
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
