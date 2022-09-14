<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class Wrapper
{
    private $f;

    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    public function __invoke(...$xs): Result
    {
        try {
            $value = ($this->f)(...$xs);
        } catch (InvalidDataException $e) {
            return Result::errors(...$e->errors);
        }

        return Result::success($value);
    }
}