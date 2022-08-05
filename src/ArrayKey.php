<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\Validation\Types;

final class ArrayKey
{
    /**
     * Return a new required ArrayKey.
     */
    public static function required(string $key): self
    {
        return new self($key, true, null);
    }

    /**
     * Return a new optional ArrayKey with the given default value.
     */
    public static function optional(string $key, mixed $default = null): self
    {
        return new self($key, false, $default);
    }

    /**
     * @var Array<callable(mixed): \Quanta\Validation\Result>
     */
    private array $validations;

    private function __construct(
        private string $key,
        private bool $required,
        private mixed $default,
        callable ...$validations
    ) {
        $this->validations = $validations;
    }

    public function int(callable ...$validations): self
    {
        return $this->then(new Types\IsInt, ...$validations);
    }

    public function float(callable ...$validations): self
    {
        return $this->then(new Types\IsFloat, ...$validations);
    }

    public function string(callable ...$validations): self
    {
        return $this->then(new Types\IsString, ...$validations);
    }

    public function array(callable ...$validations): self
    {
        return $this->then(new Types\IsArray, ...$validations);
    }

    /**
     * Return a new ArrayKey with the given validation function added.
     *
     * Bind is applied on each validation function so they are now composable.
     *
     * @param callable(mixed): \Quanta\Validation\Result ...$validations
     */
    public function then(callable ...$validations): self
    {
        if (count($validations) == 0) return $this;

        $validation = Result::bind(array_shift($validations));

        $new = new self($this->key, $this->required, $this->default, ...$this->validations, ...[$validation]);

        return $new->then(...$validations);
    }

    /**
     * Get a Result by applying required/optional validation to the given array then sequentially
     * apply each validation function on it. Nest the errors within the key at the end.
     *
     * @param mixed[] $data
     */
    public function __invoke(array $data): Result
    {
        $init = $this->init($data);

        return Composition::from(...$this->validations)->reduce($init)->nest($this->key);
    }

    /**
     * @param mixed[] $data
     */
    private function init(array $data): Result
    {
        if (array_key_exists($this->key, $data)) {
            $value = $data[$this->key];

            if (!$this->required && is_null($value)) {
                return Result::final($this->default);
            }

            return Result::success($value);
        }

        if (!$this->required) {
            return Result::final($this->default);
        }

        return Result::error('%%s is required');
    }
}
