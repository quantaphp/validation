<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\Validation\Rules;

final class ArrayKey
{
    public static function required(string $key): self
    {
        $keys = explode('.', $key);

        $head = array_shift($keys);

        $instance = new self(new Rules\Required($head));

        $reducer = fn ($instance, $key) => $instance
            ->with(new Rules\IsArray)
            ->with(new Rules\Required($key));

        return array_reduce($keys, $reducer, $instance);
    }

    public static function optional(string $key, $default = null): self
    {
        $keys = explode('.', $key);

        $head = array_shift($keys);

        $instance = new self(new Rules\Optional($head, $default));

        $reducer = fn ($instance, $key) => $instance
            ->with(new Rules\IsArray)
            ->with(new Rules\Optional($key, $default));

        return array_reduce($keys, $reducer, $instance);
    }

    public static function int(string $key, ?int ...$default): self
    {
        return self::instance($key, ...$default)->with(new Rules\IsInt);
    }

    public static function float(string $key, ?float ...$default): self
    {
        return self::instance($key, ...$default)->with(new Rules\IsFloat);
    }

    public static function string(string $key, ?string ...$default): self
    {
        return self::instance($key, ...$default)->with(new Rules\IsString);
    }

    public static function array(string $key, ?array ...$default): self
    {
        return self::instance($key, ...$default)->with(new Rules\IsArray);
    }

    private static function instance(string $key, ...$default): self
    {
        return count($default) == 0
            ? self::required($key)
            : self::optional($key, $default[0]);
    }

    /**
     * @var callable(array): \Quanta\Validation\Result
     */
    private $head;

    /**
     * @var Array<callable(mixed): \Quanta\Validation\Result>
     */
    private array $rules;

    private function __construct(callable $head, callable ...$rules)
    {
        $this->head = $head;
        $this->rules = $rules;
    }

    private function with(callable $rule): self
    {
        return new self($this->head, ...$this->rules, ...[Result::bind($rule)]);
    }

    /**
     * @param mixed[] $data
     */
    public function __invoke(array $data): Result
    {
        $init = ($this->head)($data);
        $reducer = fn ($result, $rule) => $rule($result);

        return array_reduce($this->rules, $reducer, $init);
    }
}
