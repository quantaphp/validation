<?php

namespace Quanta\Validation\Types;

abstract class AbstractFloat implements \Stringable, \JsonSerializable
{
    public function __construct(private float $value)
    {
    }

    public function value(): float
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public function jsonSerialize(): float
    {
        return $this->value;
    }
}
