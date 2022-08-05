<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class InvalidDataException extends \DomainException
{
    public static function error(string $template, mixed ...$xs): self
    {
        return new self(Error::from($template, ...$xs));
    }

    /**
     * @var \Quanta\Validation\Error[]
     */
    public readonly array $errors;

    public function __construct(Error $error, Error ...$errors)
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
