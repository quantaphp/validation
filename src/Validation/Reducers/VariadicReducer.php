<?php

declare(strict_types=1);

namespace Quanta\Validation\Reducers;

use Quanta\Validation;
use Quanta\Validation\Result;
use Quanta\Validation\AbstractInput;

final class VariadicReducer implements ReducerInterface
{
    public static function from(string|Validation|ReducerInterface $reducer): self
    {
        if (is_string($reducer)) {
            if (!is_subclass_of($reducer, AbstractInput::class)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'String validation must be the name of an implementation of %s, %s given',
                        AbstractInput::class,
                        $reducer,
                    ),
                );
            }

            $reducer = Validation::factory()->array($reducer);
        }

        if ($reducer instanceof Validation) {
            $reducer = new Reducer($reducer);
        }

        return new self(Result::variadic($reducer));
    }

    /**
     * @var callable(Result, Result): Result
     */
    private $reducer;

    private function __construct(callable $reducer)
    {
        $this->reducer = $reducer;
    }

    public function __invoke(Result $factory, Result $input): Result
    {
        return ($this->reducer)($factory, $input);
    }
}
