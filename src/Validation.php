<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Rules;
use Quanta\Validation\Types;
use Quanta\Validation\Result;

final class Validation implements ValidationInterface
{
    public static function factory(): self
    {
        return new self;
    }

    /**
     * @var array<callable(Result): Result>
     */
    private array $rules;

    private function __construct(callable ...$rules)
    {
        $this->rules = $rules;
    }

    public function key(string $key, string ...$keys): self
    {
        return $this->required($key, ...$keys);
    }

    public function required(string $key, string ...$keys): self
    {
        $instance = $this->rules(new Rules\Required($key));

        $reducer = fn (self $instance, string $key) => $instance->rules(
            new Rules\IsArray,
            new Rules\Required($key)
        );

        return array_reduce($keys, $reducer, $instance);
    }

    /**
     * @param mixed $default
     */
    public function optional($default, string $key, string ...$keys): self
    {
        $instance = $this->rules(new Rules\Optional($key, $default));

        $reducer = fn (self $instance, string $key) => $instance->rules(
            new Rules\IsArray,
            new Rules\Optional($key, $default)
        );

        return array_reduce($keys, $reducer, $instance);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function int(...$rules): self
    {
        return $this->rules(new Rules\IsInt, ...$rules);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function string(...$rules): self
    {
        return $this->rules(new Rules\IsString, ...$rules);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function float(...$rules): self
    {
        return $this->rules(new Rules\IsFloat, ...$rules);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function array(...$rules): self
    {
        return $this->rules(new Rules\IsArray, ...$rules);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function positiveInteger(...$rules): self
    {
        return $this->int(Types\PositiveInteger::class, ...$rules);
    }

    /**
     * @template T
     * @param string|callable(T): Result ...$rules
     */
    public function rules(...$rules): self
    {
        if (count($rules) == 0) return $this;

        $rule = array_shift($rules);

        if (is_string($rule) && class_exists($rule)) {
            $rule = new Rules\Wrapper(fn ($x) => new $rule($x));
        }

        if (!is_callable($rule)) {
            throw new \InvalidArgumentException('Rule must be either a callable or an existing class name');
        }

        return (new self(...$this->rules, ...[Result::bind($rule)]))->rules(...$rules);
    }

    public function __invoke(Result $factory, Result $input): Result
    {
        $input = array_reduce($this->rules, [$this, 'reducer'], $input);

        return Result::apply($factory)($input);
    }

    public function variadic(ValidationInterface $validation): ValidationInterface
    {
        return VariadicValidation::from($validation, ...$this->rules);
    }

    private function reducer(Result $input, callable $rule): Result
    {
        return $rule($input);
    }
}
