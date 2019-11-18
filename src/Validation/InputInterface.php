<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\ValidationInterface;

interface InputInterface
{
    public function bind(ValidationInterface ...$fs): InputInterface;

    /**
     * @param callable(array): mixed                                $success
     * @param callable(\Quanta\Validation\Error ...$errors): mixed  $failure
     */
    public function extract(callable $success, callable $failure);
}
