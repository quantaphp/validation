<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\Validation;

abstract class AbstractInput
{
    public static function from(array $data): static
    {
        $v = Validation::factory();
        $factory = Factory::class(static::class);

        $factory = static::validation($factory, $v);

        return $factory($data);
    }

    abstract protected static function validation(Factory $factory, Validation $v): Factory;
}
