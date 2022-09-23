<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Result;

interface ValidationInterface
{
    public function __invoke(Result $factory, Result $input): Result;
}
