<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface ErrorInterface
{
    /**
     * @return string
     */
    public function label(): string;

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return array
     */
    public function params(): array;

    /**
     * @return string
     */
    public function message(): string;
}
