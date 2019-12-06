<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface ResultInterface
{
    /**
     * @param string $key
     * @return \Quanta\Validation\Data|\Quanta\Validation\Failure
     */
    public function input(string $key): InputInterface;

    /**
     * @param callable(mixed): \Quanta\Validation\ResultInterface ...$fs
     * @return \Quanta\Validation\Success|\Quanta\Validation\Failure
     * @throws \InvalidArgumentException
     */
    public function bind(callable ...$fs): ResultInterface;

    /**
     * @param callable $success
     * @param callable $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure);
}
