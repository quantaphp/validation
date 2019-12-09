<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Bound
{
    /**
     * @var Array<int, callable(mixed): \Quanta\Validation\InputInterface>
     */
    private $fs;

    /**
     * @param callable(mixed): \Quanta\Validation\InputInterface ...$fs
     */
    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param mixed $x
     * @return \Quanta\Validation\Success|\Quanta\Validation\Failure
     */
    public function __invoke($x): InputInterface
    {
        $fs = [...$this->fs];

        $f = array_shift($fs) ?? false;

        if ($f == false) {
            return new Success($x);
        }

        return $f($x)->bind(...$fs);
    }
}
