<?php

namespace Quanta\Validation\Types;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

class IpAddress extends AbstractString
{
    public function __construct(string $value)
    {
        $filtered = filter_var($value, FILTER_VALIDATE_IP);

        if ($filtered === false) {
            throw new InvalidDataException(
                Error::from('{key} must be formatted as an ip address, %s given', ['value' => $value], self::class),
            );
        }

        parent::__construct($filtered);
    }
}
