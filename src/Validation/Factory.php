<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\VariadicValidation;
use Quanta\ValidationInterface;

final class Factory
{
    public static function from(callable $f): self
    {
        return new self(Result::pure($f));
    }

    public static function class(string $class): self
    {
        return new self(Result::pure(fn (...$xs) => new $class(...$xs)));
    }

    private function __construct(Result $f, ValidationInterface ...$validations)
    {
        $this->f = $f;
        $this->validations = $validations;
    }

    public function __invoke(array $data)
    {
        $input = Result::unit($data);

        $reducer = fn (Result $factory, ValidationInterface $validation) => $validation($factory, $input);

        $result = array_reduce($this->validations, $reducer, $this->f);

        return $result->value();
    }

    public function validation(ValidationInterface ...$validations): self
    {
        if (count($validations) == 0) return $this;

        return new self($this->f, ...$this->validations, ...$validations);
    }

    public function variadic(ValidationInterface $validation): self
    {
        return $this->validation(VariadicValidation::from($validation));
    }
}
