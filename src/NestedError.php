<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class NestedError implements ErrorInterface
{
    private ErrorInterface $error;

    private array $keys;

    public function __construct(ErrorInterface $error, string ...$keys)
    {
        $this->error = $error;
        $this->keys = $keys;
    }

    public function label(): string
    {
        return $this->error->label();
    }

    public function keys(): array
    {
        // array_values to ensure it works with implementations using
        // associative array for some reason...
        return [...$this->keys, ...array_values($this->error->keys())];
    }

    public function default(): string
    {
        return $this->error->default();
    }

    public function params(): array
    {
        return $this->error->params();
    }
}
