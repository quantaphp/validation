<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Is
{
    /**
     * @var Array<int, callable(T): (\Quanta\Validation\Error[])>
     */
    private array $rules;

    /**
     * @param callable(T): (\Quanta\Validation\Error[]) ...$rules
     */
    public function __construct(callable ...$rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param T $x
     * @return \Quanta\Validation\Success<T>|\Quanta\Validation\Failure
     */
    public function __invoke($x): InputInterface
    {
        $errors = [];

        foreach ($this->rules as $rule) {
            $errors = [...$errors, ...$rule($x)];
        }

        return count($errors) == 0
            ? new Success($x)
            : new Failure(...$errors);
    }
}
