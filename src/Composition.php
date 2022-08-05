<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Composition
{
    /**
     * @param callable(T): T ...$fs
     * @return \Quanta\Validation\Composition<T>
     */
    public static function from(callable ...$fs): self
    {
        return new self(...$fs);
    }

    /**
     * @var Array<callable(T): T>
     */
    private array $fs;

    private function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    public function reduce(mixed $init): mixed
    {
        return array_reduce($this->fs, fn ($x, $f) => $f($x), $init);
    }
}
