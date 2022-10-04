<?php

declare(strict_types=1);

namespace Quanta\Validation\Reducers;

use Quanta\Validation\Result;

interface ReducerInterface
{
    public function __invoke(Result $factory, Result $input): Result;
}
