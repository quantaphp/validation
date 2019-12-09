<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface InputInterface
{
    /**
     * @return \Quanta\Validation\Success<array<string, mixed>>|\Quanta\Validation\Failure
     */
    public function nested(string $key): InputInterface;

    /**
     * @param \Quanta\Validation\InputInterface ...$inputs
     * @return \Quanta\Validation\Success<mixed[]>|\Quanta\Validation\Failure
     */
    public function merge(InputInterface ...$inputs): InputInterface;

    /**
     * @param callable(mixed): \Quanta\Validation\InputInterface ...$fs
     * @return \Quanta\Validation\Success<mixed>|\Quanta\Validation\Failure
     * @throws \InvalidArgumentException
     */
    public function bind(callable ...$fs): InputInterface;

    /**
     * @param callable $success
     * @param callable $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure);
}
