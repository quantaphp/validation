<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\Validation\Rules\Named;
use Quanta\Validation\PartialApplications\BoundCallable;
use Quanta\Validation\PartialApplications\MappedCallable;
use Quanta\Validation\PartialApplications\AppliedCallable;
use Quanta\Validation\PartialApplications\TraversedCallable;

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
     * @return \Quanta\Validation\PartialApplications\MappedCallable
     */
    public static function map(callable $f): MappedCallable
    {
        return new MappedCallable($f);
    }

    /**
     * Input<a -> ... -> b> -> Input<a> -> ... -> Input<b>
     *
     * @param \Quanta\Validation\InputInterface $input
     * @return \Quanta\Validation\PartialApplications\AppliedCallable
     */
    public static function apply(InputInterface $input): AppliedCallable
    {
        return new AppliedCallable($input);
    }

    /**
     * (a -> Input<b>) -> ... -> (b -> Input<c>) -> Input<a> -> Input<c>
     *
     * @param callable(mixed): InputInterface ...$fs
     * @return \Quanta\Validation\PartialApplications\BoundCallable
     */
    public static function bind(callable ...$fs): BoundCallable
    {
        return new BoundCallable(...$fs);
    }

    /**
     * (a -> Input<b>) -> ... -> (b -> Input<c>) -> Array<a> -> Input<Array<c>>
     *
     * @param callable(mixed): InputInterface ...$fs
     * @return \Quanta\Validation\PartialApplications\TraversedCallable
     */
    public static function traverseA(...$fs): TraversedCallable
    {
        return new TraversedCallable(true, fn ($x, string $key) => (new Named($key, ...$fs))($x));
    }

    /**
     * (a -> Input<b>) -> ... -> (b -> Input<c>) -> Array<a> -> Input<Array<c>>
     *
     * @param callable(mixed): InputInterface ...$fs
     * @return \Quanta\Validation\PartialApplications\TraversedCallable
     */
    public static function traverseM(...$fs): TraversedCallable
    {
        return new TraversedCallable(false, fn ($x, string $key) => (new Named($key, ...$fs))($x));
    }
}
