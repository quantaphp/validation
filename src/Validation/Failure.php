<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\ValidationInterface;

final class Failure implements InputInterface
{
    /**
     * @var \Quanta\Validation\Error[]
     */
    private array $errors;

    public function __construct(Error $error, Error ...$errors)
    {
        $this->errors = [$error, ...$errors];
    }

    public function bind(ValidationInterface ...$fs): InputInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $failure(...$this->errors);
    }
}
