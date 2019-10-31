<?php

declare(strict_types=1);

namespace Quanta;

final class Field
{
    private $keys;

    private $value;

    public function __construct($value, string ...$keys)
    {
        $this->keys = $keys;
        $this->value = $value;
    }

    public function keys(): array
    {
        return $this->keys;
    }

    public function value()
    {
        return $this->value;
    }
}
