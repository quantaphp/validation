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
        return new Success(new Value($value));
    }

    /**
     * (a -> ... -> b) -> Input<a> -> ... -> Input<b>
     *
     * @param callable $f
     * @return \Quanta\Validation\PartialApplications\MappedCallable
     */
    public static function map(callable $f): PartialApplications\MappedCallable
    {
        return new PartialApplications\MappedCallable($f);
    }

    /**
     * Input<a -> ... -> b> -> Input<a> -> ... -> Input<b>
     *
     * @param \Quanta\Validation\InputInterface $input
     * @return \Quanta\Validation\PartialApplications\AppliedCallable
     */
    public static function apply(InputInterface $input): PartialApplications\AppliedCallable
    {
        return new PartialApplications\AppliedCallable($input);
    }

    /**
     * (a -> Input<b>) -> ... -> (b -> Input<c>) -> Input<a> -> Input<c>
     *
     * @param callable(mixed): InputInterface ...$fs
     * @return \Quanta\Validation\PartialApplications\BoundCallable
     */
    public static function bind(callable ...$fs): PartialApplications\BoundCallable
    {
        return new PartialApplications\BoundCallable(...$fs);
    }

    /**
     * (a -> Input<b>) -> ... -> (b -> Input<c>) -> Array<a> -> Input<Array<c>>
     *
     * @param callable(mixed): InputInterface ...$fs
     * @return \Quanta\Validation\PartialApplications\TraversedCallable
     */
    public static function traverseA(...$fs): PartialApplications\TraversedCallable
    {
        return new PartialApplications\TraversedCallable(true, ...$fs);
    }

    /**
     * (a -> Input<b>) -> ... -> (b -> Input<c>) -> Array<a> -> Input<Array<c>>
     *
     * @param callable(mixed): InputInterface ...$fs
     * @return \Quanta\Validation\PartialApplications\TraversedCallable
     */
    public static function traverseM(...$fs): PartialApplications\TraversedCallable
    {
        return new PartialApplications\TraversedCallable(false, ...$fs);
    }
}
