<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Required
{
    /**
     * @param string $key
     * @return never
     * @throws \Quanta\Validation\InvalidDataException
     */
    public function __invoke(string $key)
    {
        throw new InvalidDataException(
            new Error(sprintf('%s is required', $key), self::class, ['key' => $key]),
        );
    }
}
