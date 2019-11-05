<?php

namespace Quanta;

final class Input
{
    /**
     * a -> Input<a>
     *
     * @param mixed $value
     * @return \Quanta\Success
     */
    public static function unit($value): Success
    {
        return new Success($value);
    }

    /**
     * (a -> b) -> Input<a -> b>
     *
     * @param callable $f
     * @return \Quanta\WrappedCallable
     */
    public static function pure(callable $f): WrappedCallable
    {
        return new WrappedCallable($f);
    }

    /**
     * (a -> b) -> Input<a> -> Input<b>
     *
     * @param callable $f
     * @return callable(InputInterface $a): InputInterface
     */
    public static function map(callable $f): callable
    {
        return fn (InputInterface $input) => $input->apply(self::pure($f));
    }

    /**
     * Input<a -> b> -> Input<a> -> Input<b>
     *
     * @param \Quanta\InputInterface $f
     * @return callable(InputInterface $a): InputInterface
     */
    public static function apply(InputInterface $f): callable
    {
        return fn (InputInterface $input) => $input->apply($f);
    }

    /**
     * (a -> Input<b>) -> Input<a> -> Input<b>
     *
     * @param callable(mixed $value): InputInterface $f
     * @return callable(InputInterface $a): InputInterface
     */
    public static function bind(callable $f): callable
    {
        return fn (InputInterface $input) => $input->validate($f);
    }
}
