<?php

declare(strict_types=1);

namespace Quanta;

interface InputInterface
{
    /**
     * Call the given wrapped callable with the wrapped value.
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
     * Call the given wrapping callable with the wrapped value.
     *
     * @param callable(mixed $value): \Quanta\InputInterface ...$fs
     * @return \Quanta\InputInterface
     */
    public function validate(callable ...$fs): InputInterface;

    /**
     * Return an array of wrapped values from the wrapped array.
     *
     * @param callable(mixed $value): \Quanta\InputInterface ...$fs
     * @return \Quanta\InputInterface[]
     * @throws \LogicException
     */
    public function unpack(callable ...$fs): array;

    /**
     * Apply the given success callable on successful value or the failure callable on errors.
     *
     * @param callable(mixed $a): mixed                     $success
     * @param callable(ErrorInterface ...$errors): mixed    $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure);
}
