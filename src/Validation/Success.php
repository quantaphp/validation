<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Success implements InputInterface
{
    /**
     * @var array
     */
    private array $xs;

    /**
     * @param array $xs
     */
    public function __construct(array $xs)
    {
        $this->xs = $xs;
    }

    /**
     * @inheritdoc
     */
    public function map(callable ...$fs): InputInterface
    {
        $f = array_shift($fs) ?? false;

        return $f === false ? $this : (new self($f($this->xs)))->map(...$fs);
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

        if ($input instanceof Success) {
            return (new self(array_merge($this->xs, $input->xs)))->merge(...$inputs);
        }

        if ($input instanceof Failure) {
            return $input->merge(...$inputs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given input must be an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s given', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function bind(callable ...$fs): InputInterface
    {
        $f = array_shift($fs) ?? false;

        if ($f === false) {
            return $this;
        }

        $input = $f($this->xs);

        if ($input instanceof Success) {
            return (new self($input->xs))->bind(...$fs);
        }

        if ($input instanceof Failure) {
            return $input->bind(...$fs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given validation must return an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->xs);
    }
}
