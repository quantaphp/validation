<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface ErrorInterface
{
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function message(): string;

    /**
     * @return string
     */
    public function label(): string;

    /**
     * @return mixed[]
     */
    public function params(): array;
}
