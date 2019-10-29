<?php

declare(strict_types=1);

namespace Quanta;

final class ErrorList
{
    private $errors;

    public function __construct(string $error, string ...$errors)
    {
        $this->errors = [$error, ...$errors];
    }

    public function unshift(string ...$errors): self
    {
        return new self(...$errors, ...$this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
