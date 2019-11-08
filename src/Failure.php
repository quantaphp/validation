<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Failure implements InputInterface
{
    /**
     * @var \Quanta\Validation\ErrorInterface[]
     */
    private $errors;

    /**
     * @param \Quanta\Validation\ErrorInterface $error
     * @param \Quanta\Validation\ErrorInterface ...$errors
     */
    public function __construct(ErrorInterface $error, ErrorInterface ...$errors)
    {
        $this->errors = [$error, ...$errors];
    }

    /**
     * @param string ...$keys
     * @return \Quanta\Validation\Failure
     */
    public function nested(string ...$keys): self
    {
        if (count($keys) == 0) {
            return $this;
        }

        /** @var string */
        $key = array_pop($keys);

        return (new self(...array_map(fn ($e) => new NestedError($key, $e), $this->errors)))->nested(...$keys);
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Failure) {
            return new self(...$input->errors, ...$this->errors);
        }

        if ($input instanceof WrappedCallable) {
            return $this;
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
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(callable ...$fs): InputInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function unpack(callable ...$fs): array
    {
        return [$this];
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $failure(...$this->errors);
    }
}
