<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class Matching
{
    /**
     * @var string
     */
    private string $pattern;

    /**
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param string $x
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke(string $x): array
    {
        return preg_match($this->pattern, $x) === 1 ? [] : [
            new Error(sprintf('must match %s', $this->pattern), self::class, [
                'value' => $x, 'pattern' => $this->pattern,
            ]),
        ];
    }
}
