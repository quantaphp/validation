<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface ErrorInterface
{
    public function label(): string;

    public function keys(): array;

    public function default(): string;

    public function params(): array;
}
