<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class IsNull
{
    /**
     * @param mixed $value
     */
    public function __invoke($value): Result
    {
        return is_null($value)
            ? Result::success($value)
            : Result::error(self::class, '{key} must be null, %s given', ['found' => gettype($value)]);
    }
}
