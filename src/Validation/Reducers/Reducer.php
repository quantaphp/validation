<?php

declare(strict_types=1);

namespace Quanta\Validation\Reducers;

use Quanta\Validation;
use Quanta\Validation\Result;

final class Reducer implements ReducerInterface
{
    public function __construct(private Validation $validation)
    {
    }

    public function __invoke(Result $factory, Result $input): Result
    {
        return Result::apply($factory)(($this->validation)($input));
    }
}
