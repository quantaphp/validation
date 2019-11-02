<?php

declare(strict_types=1);

namespace Quanta;

final class NestedError implements ErrorInterface
{
    /**
     * @var \Quanta\ErrorInterface
     */
    private $error;

    /**
     * @var string[]
     */
    private $keys;

    /**
     * @param \Quanta\ErrorInterface    $error
     * @param string                    $key
     * @param string                    ...$keys
     */
    public function __construct(ErrorInterface $error, string $key, string ...$keys)
    {
        $this->error = $error;
        $this->keys = [$key, ...$keys];
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return implode('', array_map(fn ($key) => '[' . $key . ']', $this->keys)) . $this->error->name();
    }

    /**
     * @inheritdoc
     */
    public function label(): string
    {
        return $this->error->label();
    }

    /**
     * @inheritdoc
     */
    public function params(): array
    {
        return $this->error->params();
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return sprintf(vsprintf($this->label(), $this->params()), $this->name());
    }
}
