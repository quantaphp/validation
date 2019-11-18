<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Input
{
    public static function unit(string $name, $x): InputInterface
    {
        return new Success($name, $x);
    }
}
