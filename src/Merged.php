<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Merged
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
        $inputs = array_map(fn ($f) => $f($x), $this->fs);

        $input = array_shift($inputs) ?? false;

        if ($input == false) {
            return new Success($x);
        }

        return $input->merge(...$inputs);
    }
}
