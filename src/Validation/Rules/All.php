<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\RuleInterface;

final class All implements RuleInterface
{
    /**
     * @var \Quanta\Validation\RuleInterface[]
     */
    private array $rules;

    public function __construct(RuleInterface ...$rules)
    {
        $this->rules = $rules;
    }

    /**
     * @inheritdoc
     */
    public function __invoke($x): array
    {
        $errors = [];

        foreach ($this->rules as $rule) {
            $errors = [...$errors, ...$rule($x)];
        }

        return $errors;
    }
}
