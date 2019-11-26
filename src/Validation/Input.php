<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Input
{
    public static function unit(array $xs): InputInterface
    {
        return new Success($xs);
    }
}
