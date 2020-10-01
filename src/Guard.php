<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Guard
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
     * @return T
     * @throws \Quanta\Validation\InvalidDataException
     */
    public function __invoke($x)
    {
        $merge = fn ($es, $f) => [...$es, ...$f($x)];

        $errors = array_reduce($this->rules, $merge, []);

        if (count($errors) == 0) {
            return $x;
        }

        throw new InvalidDataException(...$errors);
    }
}
