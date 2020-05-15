<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class NotEmpty
{
    /**
     * @param mixed $x
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke($x): array
    {
        if (is_string($x)) {
            return strlen(trim($x)) > 0 ? [] : [
                new Error('must not be empty', self::class)
            ];
        }

        if (is_countable($x)) {
            return count($x) > 0 ? [] : [
                new Error('must not be empty', self::class)
            ];
        }

        throw new \InvalidArgumentException(
            'The given argument must be a string, an array or a countable object'
        );
    }
}
