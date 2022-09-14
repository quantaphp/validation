<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class InvalidDataException extends \DomainException
{
    /**
     * @var \Quanta\Validation\ErrorInterface[]
     */
    public array $errors;

    public function __construct(ErrorInterface $error, ErrorInterface ...$errors)
    {
        $this->errors = [$error, ...$errors];

        parent::__construct('invalid data');
    }

    /**
     * @return string[]
     */
    public function messages(ErrorFormatterInterface $formatter = null): array
    {
        return array_map($formatter ?? new ErrorFormatter, $this->errors);
    }
}