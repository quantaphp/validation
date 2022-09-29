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

    /**
     * @var ValidationInterface[]
     */
    private array $validations;

    private function __construct(private Result $f, ValidationInterface ...$validations)
    {
        $this->validations = $validations;
    }

    /**
     * @param mixed[] $data
     */
    public function __invoke(array $data): mixed
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
