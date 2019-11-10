<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class GreaterThan
{
    private $threshold;

    public function __construct(int $threshold)
    {
        $this->threshold = $threshold;
    }

    public function __invoke($value): array
    {
        if (is_int($value) || is_float($value)) {
            return $value >= $this->threshold ? [] : [new Error(
                sprintf('must be greater than or equal to %s', $this->threshold),
                self::class,
                ['threshold' => $this->threshold],
            )];
        }

        if (is_countable($value)) {
            return count($value) >= $this->threshold ? [] : [new Error(
                sprintf('must contain at least %s %s', $this->threshold, $this->threshold > 1 ? 'values' : 'value'),
                self::class,
                ['threshold' => $this->threshold],
            )];
        }

        if (is_string($value)) {
            return strlen($value) >= $this->threshold ? [] : [new Error(
                sprintf('must contain at least %s %s', $this->threshold, $this->threshold > 1 ? 'characters' : 'character'),
                self::class,
                ['threshold' => $this->threshold],
            )];
        }

        throw new \InvalidArgumentException;
    }
}
