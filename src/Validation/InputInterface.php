<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface InputInterface
{
    /**
     * @param \Quanta\Validation\InputInterface ...$inputs
     * @return \Quanta\Validation\InputInterface
     */
    public function merge(InputInterface ...$inputs): InputInterface;

    /**
     * @param callable(array): mixed                                        $success
     * @param callable(\Quanta\Validation\ErrorInterface ...$errors): mixed $failure
     */
    public function extract(callable $success, callable $failure);
}
