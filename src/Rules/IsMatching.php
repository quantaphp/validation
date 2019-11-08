<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class IsMatching
{
    private $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function __invoke(string $subject): InputInterface
    {
        return preg_match($this->pattern, $subject) === 1
            ? new Success($subject)
            : new Failure(new Error('%%s => must match %s', $this->pattern));
    }
}
