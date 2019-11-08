<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class IsNotEmpty
{
    public function __invoke(string $value): InputInterface
    {
        return strlen(trim($value)) > 0
            ? new Success($value)
            : new Failure(new Error('%%s => must not be empty'));
    }
}
