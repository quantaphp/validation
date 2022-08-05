<?php

declare(strict_types=1);

namespace Quanta\Validation;

abstract class ArrayInput
{
    abstract protected static function validation(ArrayFactory $factory): ArrayFactory;

    /**
     * @param mixed[] $data
     */
    public static function from(array $data): static
    {
        $factory = ArrayFactory::class(static::class);

        $factory = static::validation($factory);

        return $factory($data);
    }
}
