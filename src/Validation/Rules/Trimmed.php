<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class Trimmed
{
    public function __invoke(string $value): Result
    {
        return Result::success(trim($value));
    }
}
