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
     * @param string $key
     * @return \Quanta\Validation\Failure
     */
    public function nested(string $key): self
    {
        return new self(...array_map(fn ($e) => new NestedError($key, $e), $this->errors));
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Success) {
            return $this;
        }

        if ($input instanceof Failure) {
            return new self(...$input->errors, ...$this->errors);
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s given', gettype($input))
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
    public function bindkey(string $key, callable ...$fs): InputInterface
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
