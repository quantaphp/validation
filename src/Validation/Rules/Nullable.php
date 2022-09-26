<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class Nullable
{
    /**
     * @param mixed $value
     */
    public function __invoke($value): Result
    {
        return is_null($value)
            ? Result::success($value, true)
            : Result::success($value);
    }
}
