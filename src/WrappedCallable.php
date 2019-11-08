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
     * @param \Quanta\Validation\InputInterface ...$inputs
     * @return \Quanta\Validation\InputInterface
     */
    public function invoke(InputInterface ...$inputs): InputInterface
    {
        return $this(...$inputs);
    }

    /**
     * @param \Quanta\Validation\InputInterface ...$inputs
     * @return \Quanta\Validation\InputInterface
     */
    public function flatinvoke(InputInterface ...$inputs): InputInterface
    {
        return $this(...$inputs)->bind(fn ($input) => $input);
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
    public function bind(callable $f): InputInterface
    {
        $input = $f(($this->f)());

        if ($input instanceof Success || $input instanceof Failure || $input instanceof WrappedCallable) {
            return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('The given callable must return an instance of Quanta\Validation\Success|Quanta\Validation\WrappedCallable|Quanta\Validation\Failure, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function validate(callable ...$fs): InputInterface
    {
        if (count($fs) == 0) {
            return $this;
        }

        /** @var callable */
        $f = array_shift($fs);

        return $this->bind($f)->validate(...$fs);
    }

    /**
     * @inheritdoc
     */
    public function unpack(callable ...$fs): array
    {
        $value = ($this->f)();

        if (is_array($value)) {
            return array_map(function ($key, $value) use ($fs) {
                return (new Success($value, (string) $key))->validate(...$fs);
            }, array_keys($value), $value);
        }

        throw new \LogicException(sprintf('Cannot unpack %s', gettype($value)));
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success(($this->f)());
    }
}
