<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class WrappedCallable implements InputInterface
{
    /**
     * @var callable
     */
    private $f;

    /**
     * @param callable  $f
     */
    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    /**
     * @param mixed $x
     * @return \Quanta\Validation\WrappedCallable
     */
    public function curryed($x): self
    {
        return new self(fn (...$xs) => ($this->f)($x, ...$xs));
    }

    /**
     * @param \Quanta\Validation\InputInterface ...$inputs
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke(InputInterface ...$inputs): InputInterface
    {
        return array_reduce($inputs, fn ($f, $input) => $input->apply($f), $this);
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Failure) {
            return $input;
        }

        if ($input instanceof WrappedCallable) {
            return $input->curryed(($this->f)());
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\Validation\WrappedCallable|Quanta\Validation\Failure, %s given', gettype($input))
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

        $input = $f(($this->f)());

        if ($input instanceof Success || $input instanceof Failure || $input instanceof WrappedCallable) {
            return $input->bind(...$fs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given callable must return an instance of Quanta\Validation\Success|Quanta\Validation\WrappedCallable|Quanta\Validation\Failure, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success(($this->f)());
    }
}
