<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\Validation\Types;

final class VariadicFactory
{
    /**
     * Return a VariadicFactory for any given callable.
     */
    public static function from(callable $factory): self
    {
        return new self(Result::liftn($factory));
    }

    /**
     * Return a VariadicFactory returning an object of the given class name.
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

    public function __construct(callable $factory, callable ...$validations)
    {
        $this->factory = $factory;
        $this->validations = $validations;
    }

    public function int(callable ...$validations): self
    {
        return $this->then(new Types\IsInt, ...$validations);
    }

    public function float(callable ...$validations): self
    {
        return $this->then(new Types\IsFloat, ...$validations);
    }

    public function string(callable ...$validations): self
    {
        return $this->then(new Types\IsString, ...$validations);
    }

    public function array(callable ...$validations): self
    {
        return $this->then(new Types\IsArray, ...$validations);
    }

    /**
     * Return a new VariadicFactory with the given validation function added.
     *
     * Bind is applied on each validation function so they are now composable.
     *
     * @param callable(mixed): \Quanta\Validation\Result ...$validations
     */
    public function then(callable ...$validations): self
    {
        if (count($validations) == 0) return $this;

        $validation = Result::bind(array_shift($validations));

        $new = new self($this->factory, ...$this->validations, ...[$validation]);

        return $new->then(...$validations);
    }

    /**
     * Each validation function is applied on each item of the given array, then the results
     * are used as arguments to call the factory.
     *
     * @param mixed[] $data
     */
    public function __invoke(array $data): mixed
    {
        $results = [];

        foreach ($data as $key => $item) {
            $init = Result::success($item);

            $results[] = Composition::from(...$this->validations)->reduce($init)->nest((string) $key);
        }

        return ($this->factory)(...$results)->value();
    }
}
