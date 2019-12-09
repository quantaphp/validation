<?php

declare(strict_types=1);

namespace Quanta\Validation;

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
     * @inheritdoc
     */
    public function nested(string $key): InputInterface
    {
        return new Failure(...array_map(function ($error) use ($key) {
            return new NestedError($key, $error);
        }, $this->errors));
    }

    /**
     * @inheritdoc
     */
    public function merge(InputInterface ...$inputs): InputInterface
    {
        $input = array_shift($inputs) ?? false;

        if ($input === false) {
            return $this;
        }

        if ($input instanceof Success) {
            return $this;
        }

        if ($input instanceof Failure) {
            return (new self(...$this->errors, ...$input->errors))->merge(...$inputs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given input must be an instance of Quanta\Validation\Success|Quanta\Validation\Failure, instance of %s given', get_class($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function bind(callable ...$fs): self
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
