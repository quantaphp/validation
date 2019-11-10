<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Validation
{
    private $fs;

    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    public function __invoke($x): InputInterface
    {
        $errors = array_reduce($this->fs, fn ($es, $f) => array_merge($es, $f($x)), []);

        return count($errors) == 0 ? Input::unit($x) : new Failure(...$errors);
    }
}
