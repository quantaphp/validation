<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\ValidationInterface;

final class Failure implements InputInterface
{
    /**
     * @var \Quanta\Validation\ErrorInterface[]
     */
    private array $errors;

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
        $key = array_shift($keys) ?? false;

        if ($key === false) {
            return $this;
        }

        $errors = array_map(fn (ErrorInterface $error) => new NamedError($key, $error), $this->errors);

        return (new self(...$errors))->nested(...$keys);
    }

    /**
     * @inheritdoc
     */
    public function map(callable ...$fs): InputInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function merge(InputInterface ...$inputs): InputInterface
    {
        $input = array_shift($inputs) ?? false;

        if ($input == false) {
            return $this;
        }

        if ($input instanceof Success) {
            return $this->merge(...$inputs);
        }

        if ($input instanceof Failure) {
            return (new self(...$this->errors, ...$input->errors))->merge(...$inputs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given input must be an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s given', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function bind(callable ...$fs): InputInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $failure(...$this->errors);
    }
}
