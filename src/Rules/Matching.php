<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class Matching
{
    private $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function __invoke(string $value): InputInterface
    {
        return preg_match($this->pattern, $value) === 1
            ? Input::unit($value)
            : new Failure(new Error(
                sprintf('must match %s', $this->pattern),
                self::class,
                ['value' => $value, 'pattern' => $this->pattern]
            ));
    }
}
