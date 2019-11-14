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
     * (a -> ... -> c) -> (Input<a> -> ... -> Input<c>)
     *
     * Can't call it with inputs because return value would be callable|InputInterface
     *
     * @param callable $f
     * @return callable(array<int, \Quanta\Validation\InputInterface>): \Quanta\Validation\InputInterface
     */
    public static function map(callable $f): callable
    {
        return self::apply(self::unit(new CallableValue($f)));
    }

    /**
     * Input<a -> ... -> c> -> (Input<a> -> ... -> Input<c>)
     *
     * Can't call it with inputs because return value would be callable|InputInterface
     *
     * @param \Quanta\Validation\InputInterface $input
     * @return callable(array<int, \Quanta\Validation\InputInterface>): \Quanta\Validation\InputInterface
     */
    public static function apply(InputInterface $input): callable
    {
        return fn (InputInterface ...$inputs) => array_reduce($inputs, fn ($f, $x) => $x->apply($f), $input);
    }

    /**
     * ((a -> Input<b>) -> ... -> (b -> Input<c>)) -> (Input<a> -> Input<c>)
     *
     * Can't call it with input because return value would be callable|InputInterface
     *
     * @param callable(mixed): \Quanta\Validation\InputInterface ...$fs
     * @return callable(\Quanta\Validation\InputInterface): \Quanta\Validation\InputInterface
     */
    public static function bind(callable ...$fs): callable
    {
        return fn (InputInterface $input) => $input->bind(...$fs);
    }

    /**
     * String k -> ((a -> Input<b>) -> ... -> (b -> Input<c>)) -> (array<a> -> Input<c>)
     *
     * @param string                                                $key
     * @param callable(mixed): \Quanta\Validation\InputInterface    ...$fs
     * @return callable(mixed): \Quanta\Validation\InputInterface
     */
    public static function key(string $key, callable ...$fs): callable
    {
        if (count($fs) == 0) {
            return fn (array $x) => self::unit($x[$key]);
        }

        /** @var callable */
        $f = array_shift($fs);

        return function (array $x) use ($key, $f, $fs) {
            $input = $f($x[$key])->bind(...$fs);

            return $input instanceof Failure
                ? $input->nested($key)
                : $input;
        };
    }

    /**
     * Cons -> ((a -> Input<b>) -> ... -> (b -> Input<c>)) -> (array<a> -> Input<array<c>>)
     *
     * With Cons = Input<array c> -> Input<string> -> Input<c> -> Input<array c>
     *
     * Can't call it with list because return value would be callable|InputInterface
     *
     * @param callable                                              $cons
     * @param callable(mixed): \Quanta\Validation\InputInterface    ...$fs
     * @return callable(array): \Quanta\Validation\InputInterface
     */
    private static function traverse(callable $cons, callable ...$fs): callable
    {
        $unit = self::unit([]);

        return function (array $xs) use ($unit, $cons, $fs) {
            $keys = array_keys($xs);
            $reduce = fn ($input, string $key) => $cons($input, self::unit($key), self::key($key, ...$fs)($xs));
            return array_reduce($keys, $reduce, $unit);
        };
    }

    /**
     * ((a -> Input<b>) -> ... -> (b -> Input<c>)) -> (array<a> -> Input<array<c>>)
     *
     * Can't add the list in one call because return value would be callable|InputInterface
     *
     * @param callable(mixed): \Quanta\Validation\InputInterface ...$fs
     * @return callable(array): \Quanta\Validation\InputInterface
     */
    public static function traverseA(callable ...$fs): callable
    {
        $cons = self::map(fn (array $xs, string $key, $x) => array_merge($xs, [$key => $x]));

        return self::traverse($cons, ...$fs);
    }

    /**
     * ((a -> Input<b>) -> ... -> (b -> Input<c>)) -> (array<a> -> Input<array<c>>)
     *
     * Can't add the list in one call because return value would be callable|InputInterface
     *
     * @param callable(mixed): \Quanta\Validation\InputInterface ...$fs
     * @return callable(array): \Quanta\Validation\InputInterface
     */
    public static function traverseM(callable ...$fs): callable
    {
        $cons = fn ($tail, $key, $head) =>
            $key->bind(fn ($key) =>
            $head->bind(fn ($x) =>
            $tail->bind(fn ($xs) =>
            array_merge($xs, [$key => $x])
        )));

        return self::traverse($cons, ...$fs);
    }
}
