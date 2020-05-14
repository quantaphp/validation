<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class InvalidDataException extends \Exception
{
    /**
     * @var \Quanta\Validation\Error[]
     */
    private $errors;

    /**
     * @param \Quanta\Validation\Error $error
     * @param \Quanta\Validation\Error ...$errors
     */
    public function __construct(Error $error, Error ...$errors)
    {
        $this->errors = [$error, ...$errors];

        parent::__construct();
    }

    /**
     * @return \Quanta\Validation\Error[]
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
