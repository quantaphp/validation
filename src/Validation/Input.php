<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Input implements MonadInterface, InputInterface
{
    /**
     * @var mixed[]
     */
    private array $xs;

    /**
     * @param mixed[] $xs
     */
    public function __construct(array $xs)
    {
        $this->xs = $xs;
    }

    /**
     * @inheritdoc
     */
    public function result(): ResultInterface
    {
        return new Success($this->xs);
    }

    /**
     * @param callable(mixed): \Quanta\Validation\MonadInterface ...$fs
     * @return \Quanta\Validation\Input|\Quanta\Validation\Failure
     */
    public function bind(callable ...$fs): MonadInterface
    {
        $f = array_shift($fs) ?? false;

        if ($f === false) {
            return $this;
        }

        $input = $f($this->xs);

        if ($input instanceof Input) {
            return (new self($input->xs))->bind(...$fs);
        }

        if ($input instanceof Failure) {
            return $input->bind(...$fs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given validation must return an instance of Quanta\Validation\Input|Quanta\Validation\Failure, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function merge(InputInterface ...$inputs): InputInterface
    {
        $input = array_shift($inputs) ?? false;

        if ($input == false) {
            return $this;
        }

        if ($input instanceof Input) {
            return (new self(array_merge($this->xs, $input->xs)))->merge(...$inputs);
        }

        if ($input instanceof Failure) {
            return $input->merge(...$inputs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given input must be an instance of Quanta\Validation\Input|Quanta\Validation\Failure, %s given', gettype($input))
        );
    }
}
