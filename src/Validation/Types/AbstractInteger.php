<?php

namespace Quanta\Validation\Types;

abstract class AbstractInteger implements \Stringable, \JsonSerializable
{
    public function __construct(private int $value)
    {
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
