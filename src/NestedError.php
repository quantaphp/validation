<?php

declare(strict_types=1);

namespace Quanta;

final class NestedError implements ErrorInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var \Quanta\ErrorInterface
     */
    private $error;

    /**
     * @param string                    $key
     * @param \Quanta\ErrorInterface    $error
     */
    public function __construct(string $key, ErrorInterface $error)
    {
        $this->key = $key;
        $this->error = $error;
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return '[' . $this->key . ']' . $this->error->name();
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
