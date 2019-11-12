<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class LessThan
{
    private $threshold;

    public function __construct(int $threshold)
    {
        $this->threshold = $threshold;
    }

    public function __invoke($value): InputInterface
    {
        if (is_int($value) || is_float($value)) {
            return $value <= $this->threshold ? Input::unit($value) : new Failure(new Error(
                sprintf('must be less than or equal to %s', $this->threshold),
                self::class,
                ['value' => $value, 'threshold' => $this->threshold],
            ));
        }

        if (is_countable($value)) {
            return count($value) <= $this->threshold ? Input::unit($value) : new Failure(new Error(
                sprintf('must contain at most %s %s', $this->threshold, $this->threshold > 1 ? 'values' : 'value'),
                self::class,
                ['value' => $value, 'threshold' => $this->threshold],
            ));
        }

        if (is_string($value)) {
            return strlen($value) <= $this->threshold ? Input::unit($value) : new Failure(new Error(
                sprintf('must contain at most %s %s', $this->threshold, $this->threshold > 1 ? 'characters' : 'character'),
                self::class,
                ['value' => $value, 'threshold' => $this->threshold],
            ));
        }

        throw new \InvalidArgumentException('The given value is not countable');
    }
}
