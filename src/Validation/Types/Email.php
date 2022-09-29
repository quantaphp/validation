<?php

namespace Quanta\Validation\Types;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

class Email extends AbstractString
{
    public function __construct(string $value)
    {
        $filtered = filter_var($value, FILTER_VALIDATE_EMAIL);

        if ($filtered === false) {
            throw new InvalidDataException(
                new Error(self::class, '{key} must be formatted as an email, %s given', ['value' => $value])
            );
        }

        parent::__construct($filtered);
    }
}
