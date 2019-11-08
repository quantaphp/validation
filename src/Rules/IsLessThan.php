<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class IsLessThan
{
    private $threshold;

    public function __construct(int $threshold)
    {
        $this->threshold = $threshold;
    }

    public function __invoke($value): InputInterface
    {
        if ((is_int($value) || is_float($value)) && $value > $this->threshold) {
            return new Failure(new Error(
                sprintf('must be less than or equal to %s', $this->threshold),
                self::class,
                ['threshold' => $this->threshold],
            ));
        }

        if (is_array($value) && $nb = count($value) > $this->threshold) {
            return new Failure(new Error(
                sprintf('must contain at most %s %s', $this->threshold, $this->threshold > 1 ? 'values' : 'value'),
                self::class,
                ['threshold' => $this->threshold],
            ));
        }

        if (is_string($value) && ($nb = strlen($value)) > $this->threshold) {
            return new Failure(new Error(
                sprintf('must contain at most %s %s', $this->threshold, $this->threshold > 1 ? 'characters' : 'character'),
                self::class,
                ['threshold' => $this->threshold],
            ));
        }

        return new Success($value);
    }
}
