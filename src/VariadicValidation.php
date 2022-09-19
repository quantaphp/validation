<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Result;

final class VariadicValidation implements ValidationInterface
{
    private ValidationInterface $validation;

    public function __construct(ValidationInterface $validation)
    {
        $this->validation = $validation;
    }

    public function __invoke(Result $factory, $data): Result
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s::__invoke(): Argument #2 ($data) must be of type array, %s given',
                    self::class,
                    gettype($data),
                ),
            );
        }

        foreach ($data as $key => $value) {
            $factory = $this->bound($factory)(Result::unit($value)->nest((string) $key));
        }

        return $factory;
    }

    private function bound(Result $factory): callable
    {
        return Result::bind(fn ($value) => ($this->validation)($factory, $value));
    }
}
