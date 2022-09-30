<?php

namespace Quanta\Validation\Types;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

class Url extends AbstractString
{
    public function __construct(string $value)
    {
        $filtered = filter_var($value, FILTER_VALIDATE_URL);

        if ($filtered === false) {
            throw new InvalidDataException(
                Error::from('{key} must be formatted as an url, %s given', ['value' => $value], self::class),
            );
        }

        parent::__construct($filtered);
    }
}
