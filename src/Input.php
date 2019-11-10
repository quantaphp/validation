<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Input
{
    /**
     * a -> Input<a>
     *
     * @param mixed $value
     * @return \Quanta\Validation\InputInterface
     */
    public static function unit($value): InputInterface
    {
        return new Success($value);
    }

    /**
     * (a -> ... -> b) -> Input<a> -> ... -> Input<b>
     *
     * @param callable $f
     * @return callable(\Quanta\Validation\InputInterface ...$inputs): \Quanta\Validation\InputInterface
     */
    public static function map(callable $f): callable
    {
        return self::apply(self::unit($f));
    }

    /**
     * Input<a -> ... -> b> -> Input<a> -> ... -> Input<b>
     *
     * @param \Quanta\Validation\InputInterface $input
     * @return callable(\Quanta\Validation\InputInterface ...$inputs): \Quanta\Validation\InputInterface
     */
    public static function apply(InputInterface $input): callable
    {
        $reduce = fn ($f, $x) => $x->apply($f);
        $execute = fn ($f) => self::unit($f());

        return fn (InputInterface ...$inputs) => array_reduce($inputs, $reduce, $input)->bind($execute);
    }

    /**
     * (a -> Input<b>) -> Input<a> -> Input<b>
     *
     * @param callable(mixed $value): InputInterface $f
     * @return callable(\Quanta\Validation\InputInterface $input): InputInterface
     */
    public static function bind(callable $f): callable
    {
        return fn (InputInterface $input) => $input->bind($f);
    }
}
