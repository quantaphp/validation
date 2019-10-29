<?php

declare(strict_types=1);

namespace Quanta;

final class LiftedCallable
{
    private $f;

    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    public function __invoke(Input ...$xs): Input
    {
        return array_reduce($xs, fn ($f, $x) => $x->apply($f), Input::unit($this->f))->extract(
            fn (callable $f) => Input::unit($f()),
            fn (string ...$errors) => Input::invalid(...$errors),
        );
    }
}
