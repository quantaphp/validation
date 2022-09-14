<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Error implements ErrorInterface
{
    private string $label;

    private string $default;

    private array $params;

    public function __construct(string $label, string $default, ...$params)
    {
        $this->label = $label;
        $this->default = $default;
        $this->params = $params;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function keys(): array
    {
        return [];
    }

    public function default(): string
    {
        return $this->default;
    }

    public function params(): array
    {
        return $this->params;
    }
}
