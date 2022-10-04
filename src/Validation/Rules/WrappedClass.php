<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class WrappedClass
{
    public function __construct(private string $class)
    {
    }

    public function __invoke(mixed ...$xs): Result
    {
        try {
            $value = new $this->class(...$xs);
        } catch (InvalidDataException $e) {
            return Result::errors(...$e->errors);
        }

        return Result::success($value);
    }
}
