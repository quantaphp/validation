<?php

declare(strict_types=1);

namespace Quanta;

final class Field implements InputInterface
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
     * @param string    $name
     * @param mixed     $value
     * @return \Quanta\Field
     */
    public static function named(string $name, $value): self
    {
        return new self($value, $name);
    }

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
    public function validate(callable ...$fs): InputInterface
    {
        if (count($fs) == 0) {
            return $this;
        }

        /** @var callable */
        $f = array_shift($fs);

        $input = $f($this->value);

        switch (true) {
            case $input instanceof Field:
                return (new self($input->value, ...$this->keys, ...$input->keys))->validate(...$fs);
            case $input instanceof WrappedCallable:
                return $input;
            case $input instanceof ErrorList:
                return $input->nested(...$this->keys);
        }

        throw new \InvalidArgumentException(
            sprintf('The given callable must return an instance of Quanta\Field|Quanta\WrappedCallable|Quanta\ErrorList, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function unpack(callable ...$fs): array
    {
        if (is_array($this->value)) {
            return array_map(function ($key, $value) use ($fs) {
                return (new self($value, ...[...$this->keys, (string) $key]))->validate(...$fs);
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
