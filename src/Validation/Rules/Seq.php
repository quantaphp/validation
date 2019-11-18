<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\RuleInterface;

final class Seq implements RuleInterface
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
    public function __invoke(string $name, $x): array
    {
        foreach ($this->rules as $rule) {
            if (count($errors = $rule($name, $x)) > 0) {
                return $errors;
            }
        }

        return [];
    }
}
