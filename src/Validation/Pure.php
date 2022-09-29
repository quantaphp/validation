<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Pure
{
    /**
     * @var callable
     */
    private $f;

    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    /**
     * @param mixed ...$xs
     */
    public function __invoke(...$xs): mixed
    {
        return ($this->f)(...$xs);
    }

    /**
     * @param mixed $x
     */
    public function curry($x): self
    {
        return new self(fn (...$xs) => ($this->f)($x, ...$xs));
    }
}
