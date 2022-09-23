<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class IsInt
{
    /**
     * @param mixed $value
     */
    public function __invoke($value): Result
    {
        return is_int($value)
            ? Result::success($value)
            : Result::error(self::class, '{key} must be an integer, %s given', ['found' => gettype($value)]);
    }
}
