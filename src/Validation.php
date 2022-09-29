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
        $this->rule = $rules;
    }

    public function key(string $key, string ...$keys): self
    {
        $instance = $this->required($key);

        $reducer = fn (self $instance, string $key) => $instance->rule(
            new Rules\IsArray,
            new Rules\Required($key)
        );

        return array_reduce($keys, $reducer, $instance);
    }

    public function required(string $key): self
    {
        return $this->rule(new Rules\Required($key));
    }

    /**
     * @param mixed $default
     */
    public function optional(string $key, $default = null): self
    {
        return $this->rule(new Rules\Optional($key, $default));
    }

    public function null(): self
    {
        return $this->rule(new Rules\IsNull);
    }

    public function bool(): self
    {
        return $this->rule(new Rules\IsBool);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function int(...$rules): self
    {
        return $this->rule(new Rules\IsInt, ...$rules);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function string(...$rules): self
    {
        return $this->rule(new Rules\IsString, ...$rules);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function float(...$rules): self
    {
        return $this->rule(new Rules\IsFloat, ...$rules);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function array(...$rules): self
    {
        return $this->rule(new Rules\IsArray, ...$rules);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function nullable(...$rules): self
    {
        return $this->rule(new Rules\Nullable, ...$rules);
    }

    /**
     * @param string|callable(mixed): Result ...$rules
     */
    public function trimmed(...$rules): self
    {
        return $this->rule(new Rules\Trimmed, ...$rules);
    }

    public function positiveInteger(): self
    {
        return $this->int(Types\PositiveInteger::class);
    }

    public function strictlyPositiveInteger(): self
    {
        return $this->int(Types\StrictlyPositiveInteger::class);
    }

    public function nonEmptyString(bool $trimmed = true): self
    {
        return !$trimmed
            ? $this->string(Types\NonEmptyString::class)
            : $this->string(new Rules\Trimmed, Types\NonEmptyString::class);
    }

    public function email(bool $trimmed = true): self
    {
        return !$trimmed
            ? $this->string(Types\Email::class)
            : $this->string(new Rules\Trimmed, Types\Email::class);
    }

    public function url(bool $trimmed = true): self
    {
        return !$trimmed
            ? $this->string(Types\Url::class)
            : $this->string(new Rules\Trimmed, Types\Url::class);
    }

    public function ipAddress(bool $trimmed = true): self
    {
        return !$trimmed
            ? $this->string(Types\IpAddress::class)
            : $this->string(new Rules\Trimmed, Types\IpAddress::class);
    }

    /**
     * @template T
     * @param string|callable(T): Result ...$rules
     */
    public function rule(...$rules): self
    {
        if (count($rules) == 0) return $this;

        $rule = array_shift($rules);

        if (is_string($rule) && class_exists($rule)) {
            $rule = new Rules\Wrapped(fn ($x) => new $rule($x));
        }

        if (!is_callable($rule)) {
            throw new \InvalidArgumentException('Rule must be either a callable or an existing class name');
        }

        return (new self(...$this->rule, ...[Result::bind($rule)]))->rule(...$rules);
    }

    public function __invoke(Result $factory, Result $input): Result
    {
        $input = array_reduce($this->rule, [$this, 'reducer'], $input);

        return Result::apply($factory)($input);
    }

    public function variadic(ValidationInterface $validation): ValidationInterface
    {
        return VariadicValidation::from(
            $validation,
            ...$this->rule,
            ...[Result::bind(new Rules\IsArray)]
        );
    }

    private function reducer(Result $input, callable $rule): Result
    {
        return $rule($input);
    }
}
