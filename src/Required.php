<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Required
{
    /**
     * @param string $key
     * @return \Quanta\Validation\Failure
     */
    public function __invoke(string $key): InputInterface
    {
        return new Failure(
            new Error(sprintf('%s is required', $key), self::class, ['key' => $key]),
        );
    }
}
