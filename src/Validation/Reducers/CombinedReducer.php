<?php

declare(strict_types=1);

namespace Quanta\Validation\Reducers;

use Quanta\Validation;
use Quanta\Validation\Result;

final class CombinedReducer implements ReducerInterface
{
    public function __construct(private Validation $validation, private ReducerInterface $reducer)
    {
    }

    public function __invoke(Result $factory, Result $input): Result
    {
        return ($this->reducer)($factory, ($this->validation)($input));
    }
}
