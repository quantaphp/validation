<?php

declare(strict_types=1);

namespace Quanta;

final class LiftedCallable
{
    /**
     * @var callable
     */
    private $f;

    /**
     * @param callable $f
     */
    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    /**
     * @param \Quanta\Input ...$xs
     * @return \Quanta\Input
     */
    public function invoke(Input ...$xs): Input
    {
        $pure = Input::unit($this->f);
        $reduce = fn ($f, $x) => $x->apply($f);
        $execute = fn ($f) => $f();

        return array_reduce($xs, $reduce, $pure)->map($execute);
    }

    /**
     * @param \Quanta\Input ...$xs
     * @return \Quanta\Input
     */
    public function flatinvoke(Input ...$xs): Input
    {
        return $this->invoke(...$xs)->flatten();
    }

    /**
     * @param \Quanta\Input ...$xs
     * @return \Quanta\Input
     */
    public function __invoke(Input ...$xs): Input
    {
        return $this->invoke(...$xs);
    }
}
