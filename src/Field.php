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
     * @param callable(mixed): \Quanta\Validation\InputInterface                                                ...$fs
     */
    public function __construct(string $key, callable $fallback, callable ...$fs)
    {
        $this->key = $key;
        $this->fallback = $fallback;
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

        $fs = [...$this->fs];

        $f = array_shift($fs) ?? false;

        if ($f == false){
            return new Success([$this->key => $xs[$this->key]]);
        }

        return $f($xs[$this->key])->bind(...$fs)->nested($this->key);
    }
}
