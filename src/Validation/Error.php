<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Error
{
    private string $name;

    private string $message;

    private string $label;

    private array $params;

    public function __construct(string $name, string $message, string $label = '', array $params = [])
    {
        $his->name = $name;
        $this->message = $message;
        $this->label = $label;
        $this->params = $params;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function name(): string
    {
        return '';
    }

    public function label(): string
    {
        return $this->label;
    }

    public function params(): array
    {
        return $this->params;
    }
}
