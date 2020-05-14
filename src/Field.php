<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Field
{
    /**
     * @var string
     */
    public const ERROR = '%s is required';

    /**
     * @var string
     */
    private string $key;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var callable(T): \Quanta\Validation\Error[]
     */
    private $rule;

    /**
     * @param string                                    $key
     * @param callable(T): \Quanta\Validation\Error[]   ...$rules
     * @return \Quanta\Validation\Field<T>
     */
    public static function required(string $key, callable ...$rules): self
    {
        return new self($key, true, new Bound(...$rules));
    }

    /**
     * @param string                                    $key
     * @param callable(T): \Quanta\Validation\Error[]   ...$rules
     * @return \Quanta\Validation\Field<T>
     */
    public static function optional(string $key, callable ...$rules): self
    {
        return new self($key, false, new Bound(...$rules));
    }

    /**
     * @param string                                    $key
     * @param bool                                      $required
     * @param callable(T): \Quanta\Validation\Error[]   $rule
     */
    public function __construct(string $key, bool $required, callable $rule)
    {
        $this->key = $key;
        $this->required = $required;
        $this->rule = $rule;
    }

    /**
     * @param mixed[] $xs
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke(array $xs): array
    {
        if (!key_exists($this->key, $xs)) {
            return !$this->required ? [] : [
                new Error(
                    sprintf(self::ERROR, $this->key),
                    self::class,
                    ['key' => $this->key],
                )
            ];
        }

        $x = $xs[$this->key];

        $errors = ($this->rule)($x);

        return count($errors) > 0
            ? array_map(fn ($e) => $e->nest($this->key), $errors)
            : [];
    }
}
