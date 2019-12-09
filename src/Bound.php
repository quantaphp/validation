<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Bound
{
    /**
     * @var null|callable(mixed): \Quanta\Validation\InputInterface
     */
    private $f;

    /**
     * @var Array<int, callable(mixed): \Quanta\Validation\InputInterface>
     */
    private $fs;

    /**
     * @param null|callable(T): \Quanta\Validation\InputInterface   $f
     * @param callable(mixed): \Quanta\Validation\InputInterface    ...$fs
     */
    public function __construct(callable $f = null, callable ...$fs)
    {
        $this->f = $f;
        $this->fs = $fs;
    }

    /**
     * @param T $x
     * @return \Quanta\Validation\Success<mixed>|\Quanta\Validation\Failure
     */
    public function __invoke($x): InputInterface
    {
        return is_null($this->f)
            ? new Success($x)
            : ($this->f)($x)->bind(...$this->fs);
    }
}
