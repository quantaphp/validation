<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Result;

final class VariadicValidation implements ValidationInterface
{
    public static function from(ValidationInterface $validation, callable ...$rules): self
    {
        return new self(Result::variadic($validation), ...$rules);
    }

    /**
     * @var callable(Result, Result): Result
     */
    private $validation;

    /**
     * @var array<callable(Result): Result>
     */
    private array $rules;

    private function __construct(callable $validation, callable ...$rules)
    {
        $this->validation = $validation;
        $this->rules = $rules;
    }

    public function __invoke(Result $factory, Result $input): Result
    {
        $input = array_reduce($this->rules, [$this, 'reducer'], $input);

        return ($this->validation)($factory, $input);
    }

    private function reducer(Result $input, callable $rule): Result
    {
        return $rule($input);
    }
}
