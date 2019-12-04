<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class NotEmpty
{
    /**
     * @param string $x
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke(string $x): array
    {
        return strlen(trim($x)) > 0 ? [] : [
            new Error('must not be empty', self::class)
        ];
    }
}
