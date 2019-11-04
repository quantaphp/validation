<?php

namespace Quanta;

final class Input
{
    public static function unit($value): Field
    {
        return new Field($value);
    }

    public static function pure(callable $f): Field
    {
        return new Field($f);
    }

    public static function map(callable $f): callable
    {
        return function (InputInterface $input) use ($f) {
            if ($input instanceof Field || $input instanceof NamedField || $input instanceof ErrorList) {
                return $input->apply(self::pure($f));
            }

            throw new \InvalidArgumentException(
                sprintf('The given argument must be an instance of Quanta\Field|Quanta\NamedField|Quanta\ErrorList, %s given', gettype($input))
            );
        };
    }

    public static function apply(Field $f): callable
    {
        return function (InputInterface $input) use ($f) {
            if ($input instanceof Field || $input instanceof NamedField || $input instanceof ErrorList) {
                return $input->apply($f);
            }

            throw new \InvalidArgumentException(
                sprintf('The given argument must be an instance of Quanta\Field|Quanta\NamedField|Quanta\ErrorList, %s given', gettype($input))
            );
        };
    }

    public static function bind(callable $f): callable
    {
        return function (InputInterface $input) use ($f) {
            if ($input instanceof Field || $input instanceof NamedField || $input instanceof ErrorList) {
                return $input->bind($f);
            }

            throw new \InvalidArgumentException(
                sprintf('The given argument must be an instance of Quanta\Field|Quanta\NamedField|Quanta\ErrorList, %s given', gettype($input))
            );
        };
    }
}
