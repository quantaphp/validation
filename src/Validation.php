<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Rules;
use Quanta\Validation\Types;
use Quanta\Validation\Result;

final class Validation implements ValidationInterface
{
    public static function from($head, ...$tail)
    {
        return (new self(self::wrap($head)))->then(...$tail);
    }

    public static function key($key, ...$default): self
    {
        return count($default) == 0
            ? self::required($key)
            : self::optional($key, $default[0]);
    }

    public static function int($key = null, int ...$default): self
    {
        return is_null($key)
            ? self::from(new Rules\IsInt)
            : self::key($key, ...$default)->then(new Rules\IsInt);
    }

    public static function string($key = null, string ...$default): self
    {
        return is_null($key)
            ? self::from(new Rules\IsString)
            : self::key($key, ...$default)->then(new Rules\IsString);
    }

    public static function float($key = null, float ...$default): self
    {
        return is_null($key)
            ? self::from(new Rules\IsFloat)
            : self::key($key, ...$default)->then(new Rules\IsFloat);
    }

    public static function array($key = null, array ...$default): self
    {
        return is_null($key)
            ? self::from(new Rules\IsArray)
            : self::key($key, ...$default)->then(new Rules\IsArray);
    }

    public static function positiveInteger($key = null, Types\PositiveInteger ...$default): self
    {
        return is_null($key)
            ? self::from(new Rules\IsInt, Types\PositiveInteger::class)
            : self::key($key, ...$default)->then(new Rules\IsInt, Types\PositiveInteger::class);
    }

    public static function required($keys): self
    {
        if (is_string($keys)) $keys = [$keys];

        if (!is_array($keys)) {
            throw new \InvalidArgumentException;
        }

        if (count($keys) == 0) {
            throw new \LogicException;
        }

        if (count($keys) < count(array_map('is_string', $keys))) {
            throw new \LogicException;
        }

        $key = array_shift($keys);

        $init = self::from(new Rules\Required($key));

        $reducer = fn ($instance, $key) => $instance
            ->then(new Rules\IsArray)
            ->then(new Rules\Required($key));

        return array_reduce($keys, $reducer, $init);
    }

    public static function optional($keys, $default): self
    {
        if (is_string($keys)) $keys = [$keys];

        if (!is_array($keys)) {
            throw new \InvalidArgumentException;
        }

        if (count($keys) == 0) {
            throw new \LogicException;
        }

        if (count($keys) < count(array_map('is_string', $keys))) {
            throw new \LogicException;
        }

        $key = array_shift($keys);

        $init = self::from(new Rules\Optional($key, $default));

        $reducer = fn ($instance, $key) => $instance
            ->then(new Rules\IsArray)
            ->then(new Rules\Optional($key, $default));

        return array_reduce($keys, $reducer, $init);
    }

    private static function wrap($rule): callable
    {
        if (is_string($rule)) {
            if (class_exists($rule)) {
                return new Rules\Wrapper(fn ($x) => new $rule($x));
            }

            throw new \InvalidArgumentException(sprintf('\'%s\' is not an existing class name', $rule));
        }

        if (is_callable($rule)) {
            return $rule;
        }

        throw new \InvalidArgumentException('Rule must be either a callable or an existing class name');
    }

    private $head;

    private array $tail;

    private function __construct(callable $head, callable ...$tail)
    {
        $this->head = $head;
        $this->tail = $tail;
    }

    public function then(...$rules): self
    {
        if (count($rules) == 0) return $this;

        $rule = Result::bind(self::wrap((array_shift($rules))));

        return (new self($this->head, ...$this->tail, ...[$rule]))->then(...$rules);
    }

    public function __invoke(Result $factory, $data): Result
    {
        $init = ($this->head)($data);

        $reducer = fn ($result, $rule) => $rule($result);

        $result = array_reduce($this->tail, $reducer, $init);

        return Result::apply($factory)($result);
    }

    public function variadic(ValidationInterface $validation): ValidationInterface
    {
        return new CombinedValidation(
            new VariadicValidation($validation),
            $this->head,
            ...$this->tail,
            ...[Result::bind(new Rules\IsArray)],
        );
    }
}
