<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\ResultInterface;

final class Validation
{
    /**
     * @var Array<int, callable(mixed): (\Quanta\Validation\Error[])>
     */
    private array $rules;

    /**
     * @param callable(mixed): (\Quanta\Validation\Error[]) ...$rules
     */
    public function __construct(callable ...$rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param mixed $x
     * @return \Quanta\Validation\Success|\Quanta\Validation\Failure
     */
    public function __invoke($x): ResultInterface
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
