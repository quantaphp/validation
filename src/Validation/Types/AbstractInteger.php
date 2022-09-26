<?php

namespace Quanta\Validation\Types;

abstract class AbstractInteger implements \JsonSerializable
{
    private int $value;

    public function __construct(int $value)
    {
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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value;
    }
}
