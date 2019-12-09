<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Success implements InputInterface
{
    /**
     * @var T
     */
    private $x;

    /**
     * @param T $x
     */
    public function __construct($x)
    {
        $this->x = $x;
    }

    /**
     * @inheritdoc
     */
    public function nested(string $key): InputInterface
    {
        return new self([$key => $this->x]);
    }

    /**
     * @inheritdoc
     */
    public function merge(InputInterface ...$inputs): InputInterface
    {
        $input = array_shift($inputs) ?? false;

        if ($input === false) {
            return $this;
        }

        if ($input instanceof Success) {
            return (new self(array_merge($this->x, $input->x)))->merge(...$inputs);
        }

        if ($input instanceof Failure) {
            return $input->merge(...$inputs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given input must be an instance of Quanta\Validation\Success|Quanta\Validation\Failure, instance of %s given', get_class($input))
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

        $result = $f($this->x);

        if ($result instanceof Success || $result instanceof Failure) {
            return $result->bind(...$fs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given validation must return an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s returned', gettype($result))
        );
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->x);
    }
}
