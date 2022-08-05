<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class ArrayFactory
{
    /**
     * Return an ArrayFactory for any given callable.
     */
    public static function from(callable $factory): self
    {
        return new self(Result::liftn($factory));
    }

    /**
     * Return an ArrayFactory returning an object of the given class name.
     */
    public static function class(string $class): self
    {
        return self::from(fn (...$xs) => new $class(...$xs));
    }

    /**
     * @var callable(\Quanta\Validation\Result ...$xs): \Quanta\Validation\Result
     */
    private $factory;

    /**
     * @var Array<callable(\Quanta\Validation\Result): \Quanta\Validation\Result>
     */
    private array $validations;

    private function __construct(callable $factory, callable ...$validations)
    {
        $this->factory = $factory;
        $this->validations = $validations;
    }

    /**
     * Return a new ArrayFactory with the given array validation functions added.
     *
     * Bind is applied on each validation function so they are now composable.
     *
     * Each array validation function validates a parameter of the underlying factory.
     *
     * @param Array<callable(mixed[]): \Quanta\Validation\Result> ...$validations
     */
    public function validators(callable ...$validations): self
    {
        if (count($validations) == 0) return $this;

        $validation = Result::bind(array_shift($validations));

        $new = new self($this->factory, ...$this->validations, ...[$validation]);

        return $new->validators(...$validations);
    }

    /**
     * Each array validation function is applied on the given array, then the results are
     * used as arguments to call the factory.
     *
     * @param mixed[] $data
     */
    public function __invoke(array $data): mixed
    {
        $results = [];

        foreach ($this->validations as $validation) {
            $results[] = $validation(Result::success($data));
        }

        return ($this->factory)(...$results)->value();
    }
}
