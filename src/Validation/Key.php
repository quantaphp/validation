<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Key
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @var \Quanta\Validation\RuleInterface[] $rules
     */
    private array $rules;

    /**
     * @param string                            $key
     * @param \Quanta\Validation\RuleInterface  ...$rules
     */
    public function __construct(string $key, RuleInterface ...$rules)
    {
        $this->key = $key;
        $this->rules = $rules;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(array $xs): InputInterface
    {
        if (key_exists($this->key, $xs)) {
            $x = $xs[$this->key];

            $errors = $this->errors($x);

            return count($errors) == 0
                ? new Success([$this->key => $x])
                : new Failure(...$errors);
        }

        return new Failure(new Error($this->key, 'is required', self::class));
    }

    /**
     * Return the errors from the first rule returning a non empty array of errors.
     *
     * @param mixed $x
     * @return \Quanta\Validation\ErrorInterface[]
     */
    private function errors($x): array
    {
        foreach ($this->rules as $rule) {
            if (count($errors = $rule($this->key, $x)) > 0) {
                return $errors;
            }
        }

        return [];
    }
}
