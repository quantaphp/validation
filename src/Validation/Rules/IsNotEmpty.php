<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;
use Quanta\Validation\RuleInterface;

final class IsNotEmpty implements RuleInterface
{
    public function __invoke(string $name, $x): array
    {
        return strlen(trim($x)) > 0 ? [] : [
            new Error($name, 'must not be empty', self::class)
        ];
    }
}
