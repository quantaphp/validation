<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface InputInterface
{
    /**
     * Call the given wrapped callable with the wrapped value.
     *
     * @param \Quanta\Validation\InputInterface $input
     * @return \Quanta\Validation\InputInterface
     */
    public function apply(InputInterface $input): InputInterface;

    /**
     * Call the given wrapping callable with the wrapped value.
     *
     * @param callable(mixed): \Quanta\Validation\InputInterface ...$fs
     * @return \Quanta\Validation\InputInterface
     */
    public function bind(callable ...$fs): InputInterface;

    /**
     * Call the given wrapping callable with the given key of the wrapped array.
     *
     * @param string                                                $key
     * @param callable(mixed): \Quanta\Validation\InputInterface    ...$fs
     * @return \Quanta\Validation\InputInterface
     */
    public function bindkey(string $key, callable ...$fs): InputInterface;

    /**
     * Apply the given success callable on successful value or the failure callable on errors.
     *
     * @param callable(mixed): mixed                                            $success
     * @param callable(array<int, \Quanta\Validation\ErrorInterface>): mixed    $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure);
}
