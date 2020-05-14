<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Merged
{
    /**
     * @var array<int, callable(T): \Quanta\Validation\Error[]>
     */
    private $rules;

    /**
     * @param callable(T): \Quanta\Validation\Error[] ...$rules
     */
    public function __construct(callable ...$rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param T $x
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke($x): array
    {
        $errors = [];

        foreach ($this->rules as $rule) {
            $es = $rule($x);
            $es = array_values($es);
            $errors = [...$errors, ...$es];
        }

        return $errors;
    }
}
