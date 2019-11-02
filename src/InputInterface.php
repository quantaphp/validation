<?php

declare(strict_types=1);

namespace Quanta;

interface InputInterface
{
    /**
     * Map the given callable over the wrapped value.
     *
     * a -> b -> InputInterface b
     *
     * @param callable(mixed $f): mixed $f
     * @return \Quanta\InputInterface
     */
    public function map(callable $f): InputInterface;

    /**
     * Apply the given input to the wrapped callable.
     *
     * Works recursively.
     *
     * InputInterface a -> InputInterface b
     *
     * @param \Quanta\InputInterface ...$inputs
     * @return \Quanta\InputInterface
     */
    public function apply(InputInterface ...$inputs): InputInterface;

    /**
     * Apply the given wrapping callable on the wrapped value.
     *
     * Works recursively.
     *
     * a -> InputInterface b -> InputInterface b
     *
     * @param callable(mixed $a): \Quanta\InputInterface ...$fs
     * @return \Quanta\InputInterface
     */
    public function bind(callable ...$fs): InputInterface;

    /**
     * Apply the given success callable on successful value or the failure callable on errors.
     *
     * @param callable(mixed $a): mixed                     $success
     * @param callable(ErrorInterface ...$errors): mixed    $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure);
}
