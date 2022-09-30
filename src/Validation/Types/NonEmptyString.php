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
                Error::from('{key} must not be empty', ['value' => $value], self::class),
            );
        }

        parent::__construct($value);
    }
}
