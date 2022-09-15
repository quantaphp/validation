<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface ErrorFormatterInterface
{
    public function __invoke(Error $error): string;
}
