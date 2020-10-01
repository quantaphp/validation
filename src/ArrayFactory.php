<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class ArrayFactory
{
    /**
     * @var callable
     */
    private $factory;

    /**
     * @var array<int, callable(T): mixed>
     */
    private $rules;

    /**
     * @param callable              $factory
     * @param callable(T): mixed    ...$rules
     */
    public function __construct(callable $factory, callable ...$rules)
    {
        $this->factory = $factory;
        $this->rules = $rules;
    }

    /**
     * @param T $x
     * @return mixed
     * @throws \Quanta\Validation\InvalidDataException
     */
    public function __invoke($x)
    {
        $xs = [];
        $errors = [];

        foreach ($this->rules as $rule) {
            try {
                $xs[] = $rule($x);
            }

            catch (InvalidDataException $e) {
                $errors = [...$errors, ...$e->errors()];
            }
        }

        if (count($errors) == 0) {
            return ($this->factory)(...$xs);
        }

        throw new InvalidDataException(...$errors);
    }
}
