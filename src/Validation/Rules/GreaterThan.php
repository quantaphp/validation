<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class GreaterThan
{
    /**
     * @var int
     */
    private int $threshold;

    /**
     * @param int $threshold
     */
    public function __construct(int $threshold)
    {
        $this->threshold = $threshold;
    }

    /**
     * @param mixed $x
     * @return \Quanta\Validation\Error[]
     * @throws \InvalidArgumentException
     */
    public function __invoke($x): array
    {
        if (is_int($x) || is_float($x)) {
            return $x >= $this->threshold ? [] : [
                new Error(
                    sprintf('must be greater than or equal to %s', $this->threshold),
                    self::class,
                    ['value' => $x, 'threshold' => $this->threshold],
                ),
            ];
        }

        if (is_countable($x)) {
            return count($x) >= $this->threshold ? [] : [
                new Error(
                    sprintf('must contain at least %s %s', $this->threshold, $this->threshold > 1 ? 'values' : 'value'),
                    self::class,
                    ['value' => $x, 'threshold' => $this->threshold],
                ),
            ];
        }

        if (is_string($x)) {
            return strlen($x) >= $this->threshold ? [] : [
                new Error(
                    sprintf('must contain at least %s %s', $this->threshold, $this->threshold > 1 ? 'characters' : 'character'),
                    self::class,
                    ['value' => $x, 'threshold' => $this->threshold],
                ),
            ];
        }

        throw new \InvalidArgumentException('The given value is not countable');
    }
}
