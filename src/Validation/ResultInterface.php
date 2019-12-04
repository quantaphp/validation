<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface ResultInterface
{
    /**
     * @param callable ...$fs
     * @return \Quanta\Validation\ResultInterface
     */
    public function map(callable ...$fs): ResultInterface;

    /**
     * @param callable(mixed): (\Quanta\Validation\Success|\Quanta\Validation\Failure) ...$fs
     * @return \Quanta\Validation\ResultInterface
     */
    public function bind(callable ...$fs): ResultInterface;

    /**
     * @param callable(mixed): mixed                                        $success
     * @param callable(\Quanta\Validation\ErrorInterface ...$errors): mixed $failure
     */
    public function extract(callable $success, callable $failure);
}
