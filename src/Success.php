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
     * @return \Quanta\Validation\Success<array<string, T>>
     */
    public function nested(string $key): InputInterface
    {
        return new self([$key => $this->x]);
    }

    /**
     * @inheritdoc
     */
    public function merge(InputInterface $input = null, InputInterface ...$inputs): InputInterface
    {
        if (is_null($input)) {
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
     * @param null|callable(T): \Quanta\Validation\InputInterface   $f
     * @param callable(mixed): \Quanta\Validation\InputInterface    ...$fs
     * @return \Quanta\Validation\Success<mixed>|\Quanta\Validation\Failure
     * @throws \InvalidArgumentException
     */
    public function bind(callable $f = null, callable ...$fs): InputInterface
    {
        if (is_null($f)) {
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
     * @param callable(T): mixed    $success
     * @param callable              $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->x);
    }
}
