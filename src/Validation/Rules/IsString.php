<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class IsString
{
    /**
     * @param mixed $value
     */
    public function __invoke($value): Result
    {
        return is_string($value)
            ? Result::success($value)
            : Result::error(self::class, '{key} must be a string, %s given', ['found' => gettype($value)]);
    }
}
