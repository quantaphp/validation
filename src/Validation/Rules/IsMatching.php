<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;
use Quanta\Validation\RuleInterface;

final class IsMatching
{
    private $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function __invoke(string $name, $x): array
    {
        return preg_match($this->pattern, $x) === 1 ? [] : [
            new Error(
                $name,
                sprintf('must match %s', $this->pattern),
                self::class,
                ['value' => $x, 'pattern' => $this->pattern]
            ),
        ];
    }
}
