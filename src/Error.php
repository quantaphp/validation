<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Error
{
    private string $label;

    private array $keys;

    private string $default;

    private array $params;

    public static function from(string $label, string $default, ...$params): self
    {
        return new self($label, $default, $params);
    }

    private function __construct(string $label, string $default, array $params, string ...$keys)
    {
        $this->label = $label;
        $this->default = $default;
        $this->params = $params;
        $this->keys = $keys;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function keys(): array
    {
        return $this->keys;
    }

    public function default(): string
    {
        return $this->default;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function nest(string ...$keys): self
    {
        return new self($this->label, $this->default, $this->params, ...$keys, ...$this->keys);
    }
}
