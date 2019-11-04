<?php

declare(strict_types=1);

namespace Quanta;

interface InputInterface
{
    /**
     * Apply the wrapped value on the given wrapped callable.
     *
     * @param \Quanta\InputInterface $f
     * @return \Quanta\InputInterface
     */
    public function apply(InputInterface $f): InputInterface;

    /**
     * Call the given wrapping callable with the wrapped value.
     *
     * @param callable(mixed $value): \Quanta\InputInterface $f
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
