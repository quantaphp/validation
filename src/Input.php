<?php

namespace Quanta;

final class Input
{
    /**
     * The wrapped value.
     *
     * @var \Quanta\Field|\Quanta\ErrorList
     */
    private $wrapped;

    /**
     * Named constructor wrapping the given value.
     *
     * () => a -> Input a
     *
     * @param mixed     $value
     * @param string    ...$keys
     * @return \Quanta\Input
     */
    public static function unit($value, string ...$keys): self
    {
        return new self(new Field($value, ...$keys));
    }

    /**
     * Return the lifted version of the given callable.
     *
     * () => (a -> b -> ...) -> (Input a -> Input b -> ...)
     *
     * @param callable $f
     * @return \Quanta\LiftedCallable
     */
    public static function pure(callable $f): LiftedCallable
    {
        return new LiftedCallable($f);
    }

    /**
     * Named constructor wrapping the given errors.
     *
     * () => [ErrorInterface] es -> Input es
     *
     * @param \Quanta\ErrorInterface $error
     * @param \Quanta\ErrorInterface ...$errors
     * @return \Quanta\Input
     */
    public static function invalid(ErrorInterface $error, ErrorInterface ...$errors): self
    {
        return new self(new ErrorList($error, ...$errors));
    }

    /**
     * Private constructor to ensure a named constructor is used.
     *
     * Using a private constructor ensure $wrapped is either a Field or an ErrorList.
     *
     * @param \Quanta\Field|\Quanta\ErrorList $wrapped
     */
    private function __construct($wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * Apply the given callable to the wrapped value.
     *
     * Input a                      => (a -> b) -> Input b
     * Input [ErrorInterface] es    => (a -> b) -> Input[ErrorInterface] es
     *
     * @param callable $f
     * @return \Quanta\Input
     */
    public function map(callable $f): self
    {
        if ($this->wrapped instanceof Field) {
            $keys = $this->wrapped->keys();
            $value = $this->wrapped->value();

            return new self(new Field($f($value), ...$keys));
        }

        if ($this->wrapped instanceof ErrorList) {
            return new self($this->wrapped);
        }
    }

    /**
     * Apply the given wrapped callable to the wrapped value.
     *
     * Input a                      => Input (a -> b) -> Input b
     * Input a                      => Input [ErrorInterface] es -> Input [ErrorInterface] es
     * Input [ErrorInterface] es    => Input (a -> b) -> Input [ErrorInterface] es
     * Input [ErrorInterface] es1   => Input [ErrorInterface] es2 -> Input [ErrorInterface] es2 es1
     *
     * @param \Quanta\Input $input
     * @return \Quanta\Input
     */
    public function apply(self $input): self
    {
        if ($this->wrapped instanceof Field && $input->wrapped instanceof Field) {
            $x = $this->wrapped->value();
            $f = $input->wrapped->value();

            if (is_callable($f)) {
                $keys = $input->wrapped->keys();

                return new self(new Field(fn (...$xs) => $f($x, ...$xs), ...$keys));
            }

            throw new NotCallableException;
        }

        return new self(new ErrorList(
            ...($input->wrapped instanceof ErrorList ? $input->wrapped->errors() : []),
            ...($this->wrapped instanceof ErrorList ? $this->wrapped->errors() : [])
        ));
    }

    /**
     * Return the input wrapped inside this input.
     *
     * Input Input a                    => Input a
     * Input Input [ErrorInterface] es  => Input [ErrorInterface] es
     * Input [ErrorInterface] es        => Input [ErrorInterface] es
     *
     * @return \Quanta\Input
     */
    public function flatten(): self
    {
        if ($this->wrapped instanceof Field) {
            $keys1 = $this->wrapped->keys();
            $input = $this->wrapped->value();

            if ($input instanceof Input && $input->wrapped instanceof Field) {
                $keys2 = $input->wrapped->keys();
                $value = $input->wrapped->value();

                return new self(new Field($value, ...$keys1, ...$keys2));
            }

            if ($input instanceof Input && $input->wrapped instanceof ErrorList) {
                $errors = $input->wrapped->errors();

                return new self(new ErrorList(...array_map(function ($error) use ($keys1) {
                    return new NestedError($error, ...$keys1);
                }, $errors)));
            }

            throw new NotInputException;
        }

        if ($this->wrapped instanceof ErrorList) {
            return new self($this->wrapped);
        }
    }

    /**
     * Apply the given wrapping callable on the wrapped value and flatten the result.
     *
     * Input a                      => (a -> Input[b]) -> Input[b]
     * Input [ErrorInterface] es    => Input [ErrorInterface] es
     *
     * @param callable(mixed $value): \Quanta\Input ...$fs
     * @return \Quanta\Input
     */
    public function validate(callable ...$fs): self
    {
        if (count($fs) == 0) {
            return $this;
        }

        /** @var callable */
        $f = array_shift($fs);

        return $this->map($f)->flatten()->validate(...$fs);
    }

    /**
     * Apply the given wrapping callable on all the values of the wrapped array.
     *
     * Input [a]                    => (a -> Input[b]) -> [Input [b]]
     * Input [ErrorInterface] es    => (a -> Input[b]) -> [Input [ErrorInterface] es]
     *
     * @param callable(mixed $value): \Quanta\Input ...$fs
     * @return \Quanta\Input[]
     */
    public function unpack(callable ...$fs): array
    {
        if ($this->wrapped instanceof Field) {
            $keys = $this->wrapped->keys();
            $value = $this->wrapped->value();

            if (is_array($value)) {
                return array_map(function ($k, $v) use ($fs, $keys) {
                    return (new self(new Field($v, ...[...$keys, (string) $k])))->validate(...$fs);
                }, array_keys($value), $value);
            }

            throw new NotArrayException;
        }

        if ($this->wrapped instanceof ErrorList) {
            return [new self($this->wrapped)];
        }
    }

    /**
     * Extract either the input value on success or the errors on failure.
     *
     * Input a                      => (a -> b) -> ([ErrorInterface es] -> c) -> b
     * Input [ErrorInterface es]    => (a -> b) -> ([ErrorInterface es] -> c) -> c
     *
     * @param callable(mixed $value): mixed                         $success
     * @param callable(\Quanta\ErrorInterface ...$errors): mixed    $failure
     * @return mixed
     */
    public function extract(callable $success, callable $failure)
    {
        if ($this->wrapped instanceof Field) {
            return $success($this->wrapped->value());
        }

        if ($this->wrapped instanceof ErrorList) {
            return $failure(...$this->wrapped->errors());
        }
    }
}
