<?php

declare(strict_types=1);

namespace Quanta\Validation\Types;

use Quanta\Validation\Result;

final class IsFloat
{
    public function __invoke(mixed $value): Result
    {
        if (is_int($value) || is_float($value)) {
            return Result::success((float) $value);
        }

        return Result::error('%%s must be a float');
    }
}
