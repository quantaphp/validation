<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface ResultInterface
{
    /**
     * @param string $key
     * @return \Quanta\Validation\Input|\Quanta\Validation\Failure
     */
    public function input(string $key): InputInterface;

    /**
     * @param callable $success
     * @param callable $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure);
}
