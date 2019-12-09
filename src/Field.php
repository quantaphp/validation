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
     * @var callable(string): (\Quanta\Validation\Success<array<string, mixed>>|\Quanta\Validation\Failure)
     */
    private $fallback;

    /**
     * @var null|callable(mixed): \Quanta\Validation\InputInterface
     */
    private $f;

    /**
     * @var Array<int, callable(mixed): \Quanta\Validation\InputInterface>
     */
    private $fs;

    /**
     * @param string                                                $key
     * @param callable(mixed): \Quanta\Validation\InputInterface    ...$fs
     * @return \Quanta\Validation\Field
     */
    public static function required(string $key, callable ...$fs): self
    {
        return new self($key, new Required, ...$fs);
    }

    /**
     * @param string                                                $key
     * @param mixed                                                 $x
     * @param callable(mixed): \Quanta\Validation\InputInterface    ...$fs
     * @return \Quanta\Validation\Field
     */
    public static function optional(string $key, $x, callable ...$fs): self
    {
        return new self($key, new Optional($x), ...$fs);
    }

    /**
     * @param string                                                                                            $key
     * @param callable(string): (\Quanta\Validation\Success<array<string, mixed>>|\Quanta\Validation\Failure)   $fallback
     * @param null|callable(mixed): \Quanta\Validation\InputInterface                                           $f
     * @param callable(mixed): \Quanta\Validation\InputInterface                                                ...$fs
     */
    public function __construct(string $key, callable $fallback, callable $f = null, callable ...$fs)
    {
        $this->key = $key;
        $this->fallback = $fallback;
        $this->f = $f;
        $this->fs = $fs;
    }

    /**
     * @param mixed[] $xs
     * @return \Quanta\Validation\Success<array<string, mixed>>|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): InputInterface
    {
        if (! key_exists($this->key, $xs)) {
            return ($this->fallback)($this->key);
        }

        $x = $xs[$this->key];

        return is_null($this->f)
            ? new Success([$this->key => $x])
            : ($this->f)($x)->bind(...$this->fs)->nested($this->key);
    }
}
