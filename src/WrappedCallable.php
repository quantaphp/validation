<?php

declare(strict_types=1);

namespace Quanta;

final class WrappedCallable implements InputInterface
{
    /**
     * @var callable
     */
    private $f;

    /**
     * @param callable $f
     */
    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    /**
     * @param mixed $x
     * @return \Quanta\WrappedCallable
     */
    public function curryed($x): self
    {
        return new self(fn (...$xs) => ($this->f)($x, ...$xs));
    }

    /**
     * @param \Quanta\InputInterface ...$inputs
     * @return \Quanta\InputInterface
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
        switch (true) {
            case $input instanceof WrappedCallable:
                return $input->curryed(($this->f)());
            case $input instanceof ErrorList:
                return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\WrappedCallable|Quanta\ErrorList, %s given', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $f): InputInterface
    {
        $input = $f(($this->f)());

        switch (true) {
            case $input instanceof Field:
            case $input instanceof NamedField:
            case $input instanceof WrappedCallable:
            case $input instanceof ErrorList:
                return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('The given callable must return an instance of Quanta\Field|Quanta\NamedField|Quanta\WrappedCallable|Quanta\ErrorList, %s returned', gettype($input))
        );
    }

    /**
     * @return \Quanta\NamedField[]
     */
    public function unpack(): array
    {
        $value = ($this->f)();

        if (is_array($value)) {
            return array_map(function ($key, $value) {
                return NamedField::from((string) $key, new self($value));
            }, array_keys($value), $value);
        }

        throw new \LogicException(sprintf('cannot unpack %s', gettype($value)));
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success(($this->f)());
    }
}
