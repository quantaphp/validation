<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Error
{
    /**
     * @var string[]
     */
    private array $keys;

    /**
     * @param mixed[] $params
     */
    public function __construct(
        private string $label,
        private string $default,
        private array $params = [],
        string ...$keys,
    ) {
        $this->keys = $keys;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function default(): string
    {
        return $this->default;
    }

    /**
     * @return mixed[]
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * @return string[]
     */
    public function keys(): array
    {
        return $this->keys;
    }

    public function nest(string ...$keys): self
    {
        return new self($this->label, $this->default, $this->params, ...$keys, ...$this->keys);
    }
}
