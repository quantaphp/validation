<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Rule implements RuleInterface
{
    /**
     * @var callable(mixed): bool
     */
    private $predicate;

    private string $message;

    private string $label;

    private array $params;

    public function __construct(callable $predicate, string $message, string $label = '', array $params = [])
    {
        $this->predicate = $predicate;
        $this->message = $message;
        $this->label = $label;
        $this->params = $params;
    }

    public function __invoke($x): array
    {
        return ($this->predicate)($x) ? [] : [
            new Error($this->message, $this->label, $this->params)
        ];
    }
}
