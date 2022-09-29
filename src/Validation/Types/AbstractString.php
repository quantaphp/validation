<?php

namespace Quanta\Validation\Types;

abstract class AbstractString implements \Stringable, \JsonSerializable
{
    public function __construct(private string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
