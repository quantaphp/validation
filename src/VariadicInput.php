<?php

declare(strict_types=1);

namespace Quanta\Validation;

abstract class VariadicInput
{
    abstract protected static function validation(VariadicFactory $factory): VariadicFactory;

    /**
     * @param mixed[] $data
     */
    public static function from(array $data): static
    {
        $factory = VariadicFactory::class(static::class);

        $factory = static::validation($factory);

        return $factory($data);
    }
}
