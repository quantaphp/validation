<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Input
{
    /**
     * a -> Input<a>
     *
     * @param mixed $value
     * @return \Quanta\Validation\Success
     */
    public static function unit($value): Success
    {
        return new Success($value);
    }

    /**
     * (a -> b) -> Input<a -> b>
     *
     * @param callable $f
     * @return \Quanta\Validation\WrappedCallable
     */
    public static function pure(callable $f): WrappedCallable
    {
        return new WrappedCallable($f);
    }

    /**
     * (a -> b) -> Input<a> -> Input<b>
     *
     * @param callable $f
     * @return callable(\Quanta\Validation\InputInterface $a): InputInterface
     */
    public static function map(callable $f): callable
    {
        return fn (InputInterface $input) => $input->apply(self::pure($f));
    }

    /**
     * Input<a -> b> -> Input<a> -> Input<b>
     *
     * @param \Quanta\Validation\InputInterface $f
     * @return callable(\Quanta\Validation\InputInterface $a): InputInterface
     */
    public static function apply(InputInterface $f): callable
    {
        return fn (InputInterface $input) => $input->apply($f);
    }

    /**
     * (a -> Input<b>) -> Input<a> -> Input<b>
     *
     * @param callable(mixed $value): InputInterface $f
     * @return callable(\Quanta\Validation\InputInterface $a): InputInterface
     */
    public static function bind(callable $f): callable
    {
        return fn (InputInterface $input) => $input->bind($f);
    }
}
