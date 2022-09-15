<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\Validation\Rules;

final class Composition
{
    public static function from(...$rules): self
    {
        return (new self)->rule(...$rules);
    }

    private array $rules;

    private function __construct(callable ...$rules)
    {
        $this->rules = $rules;
    }

    public function __invoke(Result $init, Result $factory): Result
    {
        $reducer = fn ($result, $rule) => $rule($result);

        $result = array_reduce($this->rules, $reducer, $init);

        return Result::apply($factory)($result);
    }

    private function append(callable $f): self
    {
        return new self(...$this->rules, ...[Result::bind($f)]);
    }

    public function factory(callable $f): self
    {
        return $this->append(new Rules\Wrapper($f));
    }

    public function rule(...$rules): self
    {
        if (count($rules) == 0) return $this;

        $rule = array_shift($rules);

        if (is_string($rule)) {
            if (is_callable([$rule, 'from'])) {
                return $this->factory([$rule, 'from'])->rule(...$rules);
            }

            throw new \InvalidArgumentException(sprintf('[%s, \'from\'] is not a callable', $rule));
        }

        if (is_callable($rule)) {
            return $this->append($rule)->rule(...$rules);
        }

        throw new \InvalidArgumentException(
            'Rule must be either a callable or the name of a class with a static \'from\' method',
        );
    }
}
