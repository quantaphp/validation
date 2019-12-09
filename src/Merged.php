<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Merged
{
    /**
     * @var null|callable(T): \Quanta\Validation\InputInterface
     */
    private $f;

    /**
     * @var Array<int, callable(T): \Quanta\Validation\InputInterface>
     */
    private $fs;

    /**
     * @param null|callable(T): \Quanta\Validation\InputInterface   $f
     * @param callable(T): \Quanta\Validation\InputInterface        ...$fs
     */
    public function __construct(callable $f = null, callable ...$fs)
    {
        $this->f = $f;
        $this->fs = $fs;
    }

    /**
     * @param T $x
     * @return \Quanta\Validation\Success<T>|\Quanta\Validation\Success<mixed[]>|\Quanta\Validation\Failure
     */
    public function __invoke($x): InputInterface
    {
        return is_null($this->f)
            ? new Success($x)
            : ($this->f)($x)->merge(...array_map(fn ($f) => $f($x), $this->fs));
    }
}
