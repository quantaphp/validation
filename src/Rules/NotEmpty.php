<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class NotEmpty
{
    public function __invoke(string $value): InputInterface
    {
        return strlen(trim($value)) > 0
            ? Input::unit($value)
            : new Failure(new Error('must not be empty', self::class));
    }
}
