<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Error
{
    /**
     * @param mixed[] $params
     */
    public static function from(string $default, array $params = [], string ...$labels): self
    {
        return new self($default, $params, $labels);
    }

    /**
     * @param mixed[] $params
     * @param string[] $labels
     * @param string[] $keys
     */
    private function __construct(
        private string $default,
        private array $params = [],
        private array $labels = [],
        private array $keys = [],
    ) {
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
    public function labels(): array
    {
        return $this->labels;
    }

    /**
     * @return string[]
     */
    public function keys(): array
    {
        return $this->keys;
    }

    public function labeled(string ...$labels): self
    {
        return $this->labelled(...$labels);
    }

    public function labelled(string ...$labels): self
    {
        if (count($labels) == 0) return $this;

        return new self($this->default, $this->params, [...$this->labels, ...$labels], $this->keys);
    }

    public function nested(string ...$keys): self
    {
        if (count($keys) == 0) return $this;

        return new self($this->default, $this->params, $this->labels, [...$keys, ...$this->keys]);
    }
}
