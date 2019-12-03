<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Error implements ErrorInterface
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $message;

    /**
     * @var string
     */
    private string $label;

    /**
     * @var array
     */
    private array $params;

    /**
     * @param string    $name
     * @param string    $message
     * @param string    $label
     * @param array     $params
     */
    public function __construct(string $name, string $message, string $label = '', array $params = [])
    {
        $this->name = $name;
        $this->message = $message;
        $this->label = $label;
        $this->params = $params;
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return '[' . $this->name . ']';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @inheritdoc
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function params(): array
    {
        return $this->params;
    }
}
