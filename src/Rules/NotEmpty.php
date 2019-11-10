<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class NotEmpty
{
    public function __invoke(string $value): array
    {
        return strlen(trim($value)) > 0 ? [] : [
            new Error('must not be empty', self::class)
        ];
    }
}
