<?php

declare(strict_types=1);

namespace Quanta\Validation\Types;

use Quanta\Validation\Result;

final class IsString
{
    public function __invoke(mixed $value): Result
    {
        if (is_string($value)) {
            return Result::success($value);
        }

        return Result::error('%%s must be a string');
    }
}
