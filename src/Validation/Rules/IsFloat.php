<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class IsFloat
{
    /**
     * @param mixed $value
     */
    public function __invoke($value): Result
    {
        return is_int($value) || is_float($value)
            ? Result::success((float) $value)
            : Result::error(self::class, '{key} must be a float, %s given', ['found' => gettype($value)]);
    }
}
