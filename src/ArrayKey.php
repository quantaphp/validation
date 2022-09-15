<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\Validation\Rules;

final class ArrayKey
{
    public static function required(string $key, ...$rules): self
    {
        $keys = explode('.', $key);

        $head = array_shift($keys);

        $instance = new self(Result::bind(new Rules\Required($head)));

        $reducer = fn ($instance, $key) => $instance
            ->with(new Rules\IsArray)
            ->with(new Rules\Required($key));

        return array_reduce($keys, $reducer, $instance)->rule(...$rules);
    }

    public static function optional(string $key, mixed $default = null, ...$rules): self
    {
        $keys = explode('.', $key);

        $head = array_shift($keys);

        $instance = new self(Result::bind(new Rules\Optional($head, $default)));

        $reducer = fn ($instance, $key) => $instance
            ->with(new Rules\IsArray)
            ->with(new Rules\Optional($key, $default));

        return array_reduce($keys, $reducer, $instance)->rule(...$rules);
    }

    /**
     * @var Array<callable(mixed): \Quanta\Validation\Result>
     */
    private array $rules;

    private function __construct(callable ...$rules)
    {
        $this->rules = $rules;
    }

    public function int(...$rules): self
    {
        return $this->with(new Rules\IsInt)->rule(...$rules);
    }

    public function float(...$rules): self
    {
        return $this->with(new Rules\IsFloat)->rule(...$rules);
    }

    public function string(...$rules): self
    {
        return $this->with(new Rules\IsString)->rule(...$rules);
    }

    public function array(...$rules): self
    {
        return $this->with(new Rules\IsArray)->rule(...$rules);
    }

    public function factory(callable $f): self
    {
        return $this->with(new Rules\Wrapper($f));
    }

    /**
     * @param string|callable(mixed): \Quanta\Validation\Result ...$rules
     */
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
            return $this->with($rule)->rule(...$rules);
        }

        throw new \InvalidArgumentException(
            'Rule must be either a callable or the name of a class with a static \'from\' method',
        );
    }

    private function with(callable $rule): self
    {
        return new self(...$this->rules, ...[Result::bind($rule)]);
    }

    /**
     * @param mixed[] $data
     */
    public function __invoke(array $data): Result
    {
        $init = Result::unit($data);
        $reducer = fn ($result, $rule) => $rule($result);

        return array_reduce($this->rules, $reducer, $init);
    }
}
