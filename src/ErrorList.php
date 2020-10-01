<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class ErrorList
{
    /**
     * @var \Quanta\Validation\Error[]
     */
    private array $errors;

    /**
     * @param \Quanta\Validation\Error[] ...$errors
     */
    public function __construct(Error ...$errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param string ...$keys
     * @return \Quanta\Validation\Error[]
     */
    public function errors(string ...$keys): array
    {
        return array_map(fn ($e) => $e->nest(...$keys), $this->errors);
    }
}
