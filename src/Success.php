<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Success implements InputInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    private function value()
    {
        return $this->value instanceof CallableValue ? ($this->value)() : $this->value;
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Success) {
            if ($input->value instanceof CallableValue) {
                $f = $input->value;
                $x = $this->value();

                return new self(new CallableValue(fn (...$xs) => ($f)($x, ...$xs)));
            }

            throw new \InvalidArgumentException(
                sprintf('The given argument must be Quanta\Validation\Success(CallableValue), Quanta\Validation\Success(%s) given', gettype($input->value))
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

        $input = $f($this->value());

        if ($input instanceof Success || $input instanceof Failure) {
            return $input->bind(...$fs);
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
        return $success($this->value());
    }
}
