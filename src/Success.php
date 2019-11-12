<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Success implements InputInterface
{
    /**
     * @var \Quanta\Validation\ValueInterface
     */
    private $value;

    /**
     * @param \Quanta\Validation\ValueInterface $value
     */
    public function __construct(ValueInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Success) {
            if ($input->value instanceof CallableValue) {
                $f = $input->value;
                $x = $this->value->value();

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

        $input = $f($this->value->value());

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
        return $success($this->value->value());
    }
}
