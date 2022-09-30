<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Result;
use Quanta\Validation\AbstractInput;

final class VariadicValidation implements ValidationInterface
{
    /**
     * @param string|ValidationInterface ...$validation
     */
    public static function from(string|ValidationInterface $validation, callable ...$rules): self
    {
        if (is_string($validation)) {
            if (!is_subclass_of($validation, AbstractInput::class)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'String validation must be the name of an implementation of %s, %s given',
                        AbstractInput::class,
                        $validation,
                    ),
                );
            }

            $validation = Validation::factory()->array($validation);
        }

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
