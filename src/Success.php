<?php

declare(strict_types=1);

namespace Quanta;

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
     * @param string    $name
     * @param mixed     $value
     * @return \Quanta\Success
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
     * @param string ...$keys
     * @return \Quanta\Success
     */
    public function nested(string ...$keys): self
    {
        return count($keys) == 0 ? $this : new self($this->value, ...$keys, ...$this->keys);
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        switch (true) {
            case $input instanceof Failure:
                return $input;
            case $input instanceof WrappedCallable:
                return $input->curryed($this->value);
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\WrappedCallable|Quanta\Failure, %s given', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $f): InputInterface
    {
        $input = $f($this->value);

        switch (true) {
            case $input instanceof Success:
            case $input instanceof Failure:
            case $input instanceof WrappedCallable:
                return $input->nested(...$this->keys);
        }

        throw new \InvalidArgumentException(
            sprintf('The given callable must return an instance of Quanta\Success|Quanta\WrappedCallable|Quanta\Failure, %s returned', gettype($input))
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
        if (is_array($this->value)) {
            return array_map(function ($key, $value) use ($fs) {
                return (new self($value, ...[...$this->keys, (string) $key]))->validate(...$fs);
            }, array_keys($this->value), $this->value);
        }

        throw new \LogicException(sprintf('Cannot unpack %s', gettype($this->value)));
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->value);
    }
}
