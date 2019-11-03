<?php

declare(strict_types=1);

namespace Quanta;

interface InputInterface
{
    /**
     * Apply the given input to the wrapped callable.
     *
     * (InputInterface a -> b) -> InputInterface b
     *
     * @param \Quanta\InputInterface $input
     * @return \Quanta\InputInterface
     */
    public function apply(InputInterface $input): InputInterface;

    /**
     * Apply the given wrapping callable on the wrapped value.
     *
     * (a -> InputInterface b) -> InputInterface b
     *
     * @param callable(mixed $a): \Quanta\InputInterface $f
     * @return \Quanta\InputInterface
     */
    public function bind(callable $f): InputInterface;

    /**
     * Apply the given success callable on successful value or the failure callable on errors.
     *
     * @param callable(mixed $a): mixed                     $success
     * @param callable(ErrorInterface ...$errors): mixed    $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure);
}
