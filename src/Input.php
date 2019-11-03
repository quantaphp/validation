<?php

namespace Quanta;

final class Input
{
    public static function unit($value): InputInterface
    {
        return new Field(fn () => $value);
    }

    public static function pure(callable $f): InputInterface
    {
        return new Field($f);
    }

    public static function map(callable $f): callable
    {
        return fn (InputInterface $input) => self::pure($f)->apply($input);
    }

    public static function apply(InputInterface $f): callable
    {
        return fn (InputInterface $input) => $input->apply($f);
    }

    public static function bind(callable $f): callable
    {
        return fn (InputInterface $input) => $input->bind($f);
    }
}
