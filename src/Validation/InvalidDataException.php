<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class InvalidDataException extends \DomainException
{
    private static ?ErrorFormatter $default = null;

    private static function defaultFormatter(): ErrorFormatter
    {
        if (!self::$default) {
            self::$default = new ErrorFormatter;
        }

        return self::$default;
    }

    /**
     * @var Error[]
     */
    private array $errors;

    public function __construct(Error $error, Error ...$errors)
    {
        $formatter = self::defaultFormatter();

        $this->errors = [$error, ...$errors];

        parent::__construct($formatter($error));
    }

    public function result(): Result
    {
        return Result::errors(...$this->errors);
    }

    /**
     * @return string[]
     */
    public function messages(ErrorFormatterInterface $formatter = null): array
    {
        return array_map($formatter ?? self::defaultFormatter(), $this->errors);
    }
}
