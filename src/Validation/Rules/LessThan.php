<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;
use Quanta\Validation\RuleInterface;

final class LessThan implements RuleInterface
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
     * @inheritdoc
     */
    public function __invoke(string $name, $x): array
    {
        if (is_int($x) || is_float($x)) {
            return $x <= $this->threshold ? [] : [
                new Error(
                    $name,
                    sprintf('must be less than or equal to %s', $this->threshold),
                    self::class,
                    ['value' => $x, 'threshold' => $this->threshold],
                ),
            ];
        }

        if (is_countable($x)) {
            return count($x) <= $this->threshold ? [] : [
                new Error(
                    $name,
                    sprintf('must contain at most %s %s', $this->threshold, $this->threshold > 1 ? 'values' : 'value'),
                    self::class,
                    ['value' => $x, 'threshold' => $this->threshold],
                ),
            ];
        }

        if (is_string($x)) {
            return strlen($x) <= $this->threshold ? [] : [
                new Error(
                    $name,
                    sprintf('must contain at most %s %s', $this->threshold, $this->threshold > 1 ? 'characters' : 'character'),
                    self::class,
                    ['value' => $x, 'threshold' => $this->threshold],
                ),
            ];
        }

        throw new \InvalidArgumentException('The given value is not countable');
    }
}
