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
     * @var string
     */
    private string $message;

    /**
     * @var string
     */
    private string $label;

    /**
     * @var mixed[]
     */
    private array $params;

    /**
     * @param string    $message
     * @param string    $label
     * @param mixed[]   $params
     */
    public function __construct(string $message, string $label = '', array $params = [])
    {
        $this->keys = [];
        $this->message = $message;
        $this->label = $label;
        $this->params = $params;
    }

    /**
     * @param string $key
     * @return \Quanta\Validation\Error
     */
    public function nest(string $key): self
    {
        $this->keys = [$key, ...$this->keys];

        return $this;
    }

    /**
     * @return string[]
     */
    public function keys(): array
    {
        return $this->keys;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * @return mixed[]
     */
    public function params(): array
    {
        return $this->params;
    }
}
