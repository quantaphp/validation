<?php

declare(strict_types=1);

namespace Quanta;

final class NamedError implements ErrorInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Quanta\ErrorInterface
     */
    private $error;

    /**
     * @param string                    $name
     * @param \Quanta\ErrorInterface    $error
     */
    public function __construct(string $name, ErrorInterface $error)
    {
        $this->name = $name;
        $this->error = $error;
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return '[' . $this->name . ']' . $this->error->name();
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
