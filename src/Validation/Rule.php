<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Rule implements RuleInterface
{
    /**
     * @var callable(mixed): bool
     */
    private $predicate;

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
     * @param callable(mixed): bool $predicate
     * @param string                $message
     * @param string                $label
     * @param array                 $params
     */
    public function __construct(callable $predicate, string $message, string $label = '', array $params = [])
    {
        $this->predicate = $predicate;
        $this->message = $message;
        $this->label = $label;
        $this->params = $params;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(string $name, $x): array
    {
        return ($this->predicate)($x) ? [] : [
            new Error($name, $this->message, $this->label, $this->params)
        ];
    }
}
