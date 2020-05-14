<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Bound
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
        foreach ($this->rules as $rule) {
            $errors = $rule($x);

            if (count($errors) > 0) {
                return array_values($errors);
            }
        }

        return [];
    }
}
