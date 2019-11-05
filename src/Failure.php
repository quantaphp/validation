<?php

declare(strict_types=1);

namespace Quanta;

final class Failure implements InputInterface
{
    /**
     * @var \Quanta\ErrorInterface[]
     */
    private $errors;

    /**
     * @param string                    $name
     * @param \Quanta\ErrorInterface    $error
     * @param \Quanta\ErrorInterface    ...$errors
     * @return \Quanta\Failure
     */
    public static function named(string $name, ErrorInterface $error, ErrorInterface ...$errors): self
    {
        return (new self($error, ...$errors))->nested($name);
    }

    /**
     * @param \Quanta\ErrorInterface    $error
     * @param \Quanta\ErrorInterface    ...$errors
     */
    public function __construct(ErrorInterface $error, ErrorInterface ...$errors)
    {
        $this->errors = [$error, ...$errors];
    }

    /**
     * @param string ...$keys
     * @return \Quanta\Failure
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
        if ($input instanceof WrappedCallable) {
            return $this;
        }

        if ($input instanceof Failure) {
            return new self(...$input->errors, ...$this->errors);
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\WrappedCallable|Quanta\Failure, %s given', gettype($input))
        );
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
