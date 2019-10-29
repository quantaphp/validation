<?php

namespace Quanta;

final class Input
{
    /**
     * The wrapped value.
     *
     * @var \Quanta\Value|\Quanta\ErrorList
     */
    private $wrapped;

    /**
     * Named constructor lifting a single value.
     *
     * @param mixed $value
     * @return \Quanta\Input
     */
    public static function unit($value): self
    {
        return new self(new Value($value));
    }

    /**
     * Named constructor for invalid input.
     *
     * @param string $error
     * @param string ...$errors
     * @return \Quanta\Input
     */
    public static function invalid(string $error, string ...$errors): self
    {
        return new self(new ErrorList($error, ...$errors));
    }

    /**
     * Private constructor to ensure a named constructor is used.
     *
     * Using a private constructor ensure $wrapped is either Value or ErrorList.
     *
     * @param \Quanta\Value|\Quanta\ErrorList $wrapped
     */
    private function __construct($wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * Apply the given callable to the wrapped value.
     *
     * @param callable $f
     * @return \Quanta\Input
     */
    public function map(callable $f): self
    {
        if ($this->wrapped instanceof Value) {
            return self::unit($f($this->wrapped->value()));
        }

        if ($this->wrapped instanceof ErrorList) {
            return new self($this->wrapped);
        }
    }

    /**
     * Apply the given wrapped callable to the wrapped value.
     *
     * @param \Quanta\Input $input
     * @return \Quanta\Input
     * @throws \InvalidArgumentException
     */
    public function apply(self $input): self
    {
        try {
            return $input->extract(
                function ($f) {
                    if (! is_callable($f)) {
                        throw new NotCallableException;
                    }

                    if ($this->wrapped instanceof Value) {
                        return new self(new Value(fn (...$xs) => $f($this->wrapped->value(), ...$xs)));
                    }

                    if ($this->wrapped instanceof ErrorList) {
                        return new self($this->wrapped);
                    }
                },
                function (string ...$errors) {
                    if ($this->wrapped instanceof Value) {
                        return new self(new ErrorList(...$errors));
                    }

                    if ($this->wrapped instanceof ErrorList) {
                        return new self($this->wrapped->unshift(...$errors));
                    }
                },
            );
        }

        catch (NotCallableException $e) {
            throw new \InvalidArgumentException(
                'apply() error: the given instance of Input does not contain a callable'
            );
        }
    }

    /**
     * Return the input wrapped inside this input.
     *
     * @return \Quanta\Input
     * @throws \LogicException
     */
    public function flattened(): self
    {
        if ($this->wrapped instanceof Value) {
            $value = $this->wrapped->value();

            if ($value instanceof Input) {
                return $value;
            }

            throw new \LogicException(
                'flattened() error: this instance of Input does not contain another instance of Input'
            );
        }

        if ($this->wrapped instanceof ErrorList) {
            return $this;
        }
    }

    /**
     * Apply the given lifting callable on the wrapped value.
     *
     * === $this->map($f)->flattened() but better exceptions by doing this.
     *
     * @param callable(mixed $value): \Quanta\Input $f
     * @return \Quanta\Input
     * @throws \InvalidArgumentException
     */
    public function fmap(callable $f): self
    {
        if ($this->wrapped instanceof Value) {
            $value = $f($this->wrapped->value());

            if ($value instanceof Input) {
                return $value;
            }

            throw new \InvalidArgumentException(
                'fmap() error: the given callable does not return an instance of Input'
            );
        }

        if ($this->wrapped instanceof ErrorList) {
            return new self($this->wrapped);
        }
    }

    /**
     * Extract either the input value on success or the errors on failure.
     *
     * @param callable(mixed $value): mixed         $success
     * @param callable(string ...$errors): mixed    $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure)
    {
        if ($this->wrapped instanceof Value) {
            return $success($this->wrapped->value());
        }

        if ($this->wrapped instanceof ErrorList) {
            return $failure(...$this->wrapped->errors());
        }
    }
}
