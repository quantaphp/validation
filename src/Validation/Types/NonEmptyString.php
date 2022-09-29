<?php

namespace Quanta\Validation\Types;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

class NonEmptyString extends AbstractString
{
    public function __construct(string $value)
    {
        if ($value == '') {
            throw new InvalidDataException(
                new Error(self::class, '{key} must not be empty', ['value' => $value])
            );
        }

        parent::__construct($value);
    }
}
