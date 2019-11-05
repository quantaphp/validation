<?php

declare(strict_types=1);

namespace Quanta;

final class Field implements InputInterface
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
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        switch (true) {
            case $input instanceof WrappedCallable:
                return $input->curryed($this->value);
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
        $input = $f($this->value);

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
        if (is_array($this->value)) {
            return array_map(function ($key, $value) {
                return NamedField::from((string) $key, new self($value));
            }, array_keys($this->value), $this->value);
        }

        throw new \LogicException(sprintf('cannot unpack %s', gettype($this->value)));
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->value);
    }
}
