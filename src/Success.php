<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Success implements InputInterface
{
    /**
     * @var string[]
     */
    private $keys;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed     $value
     * @param string    ...$keys
     */
    public function __construct($value, string ...$keys)
    {
        $this->keys = $keys;
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Success) {
            if (is_callable($input->value)) {
                return new self(fn (...$xs) => ($input->value)($this->value, ...$xs));
            }

            throw new \InvalidArgumentException(
                sprintf('The given argument must be Quanta\Validation\Success(callable), Quanta\Validation\Success(%s) given', gettype($input->value))
            );
        }

        if ($input instanceof Failure) {
            return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s given', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function bind(callable ...$fs): InputInterface
    {
        if (count($fs) == 0) {
            return $this;
        }

        /** @var callable */
        $f = array_shift($fs);

        $input = $f($this->value);

        if ($input instanceof Success) {
            return (new self($input->value, ...$this->keys, ...$input->keys))->bind(...$fs);
        }

        if ($input instanceof Failure) {
            return $input->nested(...$this->keys)->bind(...$fs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given callable must return an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->value);
    }
}
