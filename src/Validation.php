<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\InvalidDataException;

/**
 * @template T
 * @template R
 */
final class Validation
{
    /**
     * @var callable(...mixed): R
     */
    private $factory;

    /**
     * @var array<int, callable(T): mixed>
     */
    private $rules;

    /**
     * @param callable(mixed): R    $factory
     * @param callable(T): mixed    ...$rules
     */
    public function __construct(callable $factory, callable ...$rules)
    {
        $this->factory = $factory;
        $this->rules = $rules;
    }

    /**
     * @param T $x
     * @return R
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
