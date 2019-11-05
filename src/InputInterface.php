<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface InputInterface
{
    /**
     * Nest the input within the given keys.
     *
     * @param string ...$keys
     * @return \Quanta\Validation\InputInterface
     */
    public function nested(string ...$keys): InputInterface;

    /**
     * Call the given wrapped callable with the wrapped value.
     *
     * @param \Quanta\Validation\InputInterface $f
     * @return \Quanta\Validation\InputInterface
     */
    public function apply(InputInterface $f): InputInterface;

    /**
     * Call the given wrapping callable with the wrapped value.
     *
     * @param callable(mixed $value): \Quanta\Validation\InputInterface $f
     * @return \Quanta\Validation\InputInterface
     */
    public function bind(callable $f): InputInterface;

    /**
     * Call the given wrapping callable with the wrapped value.
     *
     * @param callable(mixed $value): \Quanta\Validation\InputInterface ...$fs
     * @return \Quanta\Validation\InputInterface
     */
    public function validate(callable ...$fs): InputInterface;

    /**
     * Return an array of wrapped values from the wrapped array.
     *
     * @param callable(mixed $value): \Quanta\Validation\InputInterface ...$fs
     * @return \Quanta\Validation\InputInterface[]
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
