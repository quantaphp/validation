<?php

declare(strict_types=1);

namespace Quanta;

final class Error implements ErrorInterface
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $params;

    /**
     * @param string    $label
     * @param mixed     ...$params
     */
    public function __construct(string $label, ...$params)
    {
        $this->label = $label;
        $this->params = $params;
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

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return sprintf(vsprintf($this->label, $this->params), 'Input');
    }
}
