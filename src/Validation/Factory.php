<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\Validation;
use Quanta\Validation\Reducers\Reducer;
use Quanta\Validation\Reducers\VariadicReducer;
use Quanta\Validation\Reducers\ReducerInterface;

final class Factory
{
    public static function from(callable $f): self
    {
        return new self(Result::pure($f));
    }

    public static function class(string $class): self
    {
        return new self(Result::pure(fn (...$xs) => new $class(...$xs)));
    }

    /**
     * @var ReducerInterface[]
     */
    private array $reducers;

    private function __construct(private Result $f, ReducerInterface ...$reducers)
    {
        $this->reducers = $reducers;
    }

    /**
     * @param mixed[] $data
     */
    public function __invoke(array $data): mixed
    {
        $input = Result::unit($data);

        $reducer = fn (Result $factory, ReducerInterface $reducer) => $reducer($factory, $input);

        $result = array_reduce($this->reducers, $reducer, $this->f);

        return $result->value();
    }

    public function validation(Validation|ReducerInterface ...$reducers): self
    {
        if (count($reducers) == 0) return $this;

        $reducer = array_shift($reducers);

        if ($reducer instanceof Validation) {
            $reducer = new Reducer($reducer);
        }

        $instance = new self($this->f, ...$this->reducers, ...[$reducer]);

        return $instance->validation(...$reducers);
    }

    /**
     * @param string|Validation|ReducerInterface ...$reducer
     */
    public function variadic(string|Validation|ReducerInterface $reducer): self
    {
        return new self($this->f, ...$this->reducers, ...[VariadicReducer::from($reducer)]);
    }
}
