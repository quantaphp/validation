<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Error implements ErrorInterface
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $params;

    /**
     * @param string    $message
     * @param string    $label
     * @param array     $params
     */
    public function __construct(string $message, string $label = '', array $params = [])
    {
        $this->message = $message;
        $this->label = $label;
        $this->params = $params;
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
    public function name(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return $this->message;
    }
}
