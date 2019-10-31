<?php

declare(strict_types=1);

namespace Quanta;

final class ErrorList
{
    /**
     * @var \Quanta\ErrorInterface[]
     */
    private $errors;

    /**
     * @param \Quanta\ErrorInterface $error
     * @param \Quanta\ErrorInterface ...$errors
     */
    public function __construct(ErrorInterface $error, ErrorInterface ...$errors)
    {
        $this->errors = [$error, ...$errors];
    }

    /**
     * @return \Quanta\ErrorInterface[]
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
