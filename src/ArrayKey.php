<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\Validation\Rules;

final class ArrayKey
{
    public static function required(string $key, callable ...$rules): self
    {
        return (new self(Result::bind(new Rules\Required($key))))->rule(...$rules);
    }

    public static function optional(string $key, mixed $default = null, ...$rules): self
    {
        return (new self(Result::bind(new Rules\Optional($key, $default))))->rule(...$rules);
    }

    /**
     * @var Array<callable(mixed): \Quanta\Validation\Result>
     */
    private array $rules;

    private function __construct(callable ...$rules)
    {
        $this->rules = $rules;
    }

    public function int(callable ...$rules): self
    {
        return $this->rule(new Rules\IsInt, ...$rules);
    }

    public function float(callable ...$rules): self
    {
        return $this->rule(new Rules\IsFloat, ...$rules);
    }

    public function string(callable ...$rules): self
    {
        return $this->rule(new Rules\IsString, ...$rules);
    }

    public function array(callable ...$rules): self
    {
        return $this->rule(new Rules\IsArray, ...$rules);
    }

    public function key(string $key, callable ...$rules): self
    {
        return $this->rule(new Rules\IsArray, new Rules\Required($key), ...$rules);
    }

    public function factory(callable $f): self
    {
        return $this->rule(new Rules\Wrapper($f));
    }

    public function to(string $class): self
    {
        $f = [$class, 'from'];

        if (is_callable($f)) {
            return $this->factory($f);
        }

        throw new \InvalidArgumentException(sprintf('[%s, \'from\'] must be a callable', $class));
    }

    /**
     * @param callable(mixed): \Quanta\Validation\Result ...$rules
     */
    public function rule(callable ...$rules): self
    {
        if (count($rules) == 0) return $this;

        $rule = Result::bind(array_shift($rules));

        return (new self(...$this->rules, ...[$rule]))->rule(...$rules);
    }

    /**
     * @param mixed[] $data
     */
    public function __invoke(array $data): Result
    {
        return array_reduce($this->rules, fn ($result, $rule) => $rule($result), Result::unit($data));
    }
}
