<?php

declare(strict_types=1);

use Quanta\Validation\Error;
use Quanta\Validation\ErrorFormatterInterface;

final class TestErrorFormatter implements ErrorFormatterInterface
{
    public function __invoke(Error $error): string
    {
        return implode(':', [
            $error->label(),
            $error->default(),
            ...array_values($error->params()),
            ...$error->keys(),
        ]);
    }
}
