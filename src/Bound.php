<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Bound
{
    /**
     * @var array<int, callable(mixed): mixed>
     */
    private $rules;

    /**
     * @param callable(mixed): mixed ...$rules
     */
    public function __construct(callable ...$rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param mixed $x
     * @return mixed
     * @throws \Quanta\Validation\InvalidDataException
     */
    public function __invoke($x)
    {
        return array_reduce($this->rules, fn ($x, $f) => $f($x), $x);
    }
}
