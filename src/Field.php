<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Field
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @var callable(string): mixed
     */
    private $fallback;

    /**
     * @var callable(mixed): mixed
     */
    private $rule;

    /**
     * @param string                    $key
     * @param callable(mixed): mixed    ...$rules
     * @return \Quanta\Validation\Field
     */
    public static function required(string $key, callable ...$rules): self
    {
        return new self($key, new Required, new Bound(...$rules));
    }

    /**
     * @param string                    $key
     * @param mixed                     $x
     * @param callable(mixed): mixed    ...$rules
     * @return \Quanta\Validation\Field
     */
    public static function optional(string $key, $x, callable ...$rules): self
    {
        return new self($key, new Optional($x), new Bound(...$rules));
    }

    /**
     * @param string                    $key
     * @param callable(string): mixed   $fallback
     * @param callable(mixed): mixed    $rule
     */
    public function __construct(string $key, callable $fallback, callable $rule)
    {
        $this->key = $key;
        $this->fallback = $fallback;
        $this->rule = $rule;
    }

    /**
     * @return callable(mixed[]): mixed
     */
    public function focus(): callable
    {
        return new Bound($this, new Focus($this->key));
    }

    /**
     * @param mixed[] $xs
     * @return mixed[]
     * @throws \Quanta\Validation\InvalidDataException
     */
    public function __invoke(array $xs): array
    {
        if (!key_exists($this->key, $xs)) {
            return [$this->key => ($this->fallback)($this->key)];
        }

        $x = $xs[$this->key];

        try {
            return [$this->key => ($this->rule)($x)];
        }

        catch (InvalidDataException $e) {
            throw $e->nest($this->key);
        }
    }
}
