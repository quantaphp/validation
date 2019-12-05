<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\ResultInterface;

/**
 * @template T
 */
final class Validation
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
     * @return \Quanta\Validation\Success|\Quanta\Validation\Failure
     */
    public function __invoke($x): ResultInterface
    {
        foreach ($this->rules as $rule) {
            if (count($errors = $rule($x)) > 0) {
                return new Failure(...$errors);
            }
        }

        return new Success($x);
    }
}
