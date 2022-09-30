<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class IsFloat
{
    public function __invoke(mixed $value): Result
    {
        return is_int($value) || is_float($value)
            ? Result::success((float) $value)
            : Result::error('{key} must be a float, %s given', ['type' => gettype($value)], self::class);
    }
}
