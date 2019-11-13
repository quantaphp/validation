<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

final class Combined
{
    /**
     * @var array<int, callable(mixed): \Quanta\Validation\InputInterface> $fs
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
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke($x): InputInterface
    {
        if (count($this->fs) == 0) {
            return Input::unit($x);
        }

        $f = $this->fs[0];
        $fs = array_slice($this->fs, 1);

        return $f($x)->bind(...$fs);
    }
}
