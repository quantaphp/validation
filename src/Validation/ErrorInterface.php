<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface ErrorInterface
{
    /**
     * Return the error name.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Return the error message.
     *
     * @return string
     */
    public function message(): string;

    /**
     * Return the error label.
     *
     * @return string
     */
    public function label(): string;

    /**
     * Return the error params.
     *
     * @return array
     */
    public function params(): array;
}
