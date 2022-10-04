<?php

namespace Quanta\Validation\Types;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

abstract class AbstractStringMatching extends AbstractString
{
    abstract protected static function pattern(): string;

    public function __construct(string $value)
    {
        $pattern = static::pattern();

        if (preg_match($pattern, $value) === 0) {
            throw new InvalidDataException(
                Error::from('{key} must match %s', ['pattern' => $pattern]),
            );
        }

        parent::__construct($value);
    }
}
