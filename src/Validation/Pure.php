<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Pure
{
    private $f;

    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    public function __invoke(...$xs)
    {
        return ($this->f)(...$xs);
    }

    public function curry($x): self
    {
        return new self(fn (...$xs) => ($this->f)($x, ...$xs));
    }
}
