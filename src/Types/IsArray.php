<?php

declare(strict_types=1);

namespace Quanta\Validation\Types;

use Quanta\Validation\Result;

final class IsArray
{
    public function __invoke(mixed $value): Result
    {
        if (is_array($value)) {
            return Result::success($value);
        }

        return Result::error('%%s must be an array');
    }
}
