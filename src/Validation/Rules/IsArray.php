<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class IsArray
{
    public function __invoke(mixed $value): Result
    {
        return is_array($value)
            ? Result::success($value)
            : Result::error(self::class, '{key} must be an array, %s given', gettype($value));
    }
}
