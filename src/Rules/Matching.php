<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class Matching
{
    private $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function __invoke(string $subject): array
    {
        return preg_match($this->pattern, $subject) === 1 ? [] : [
            new Error(
                sprintf('must match %s', $this->pattern),
                self::class,
                ['pattern' => $this->pattern]
            )
        ];
    }
}
