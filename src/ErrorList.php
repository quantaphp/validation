<?php

declare(strict_types=1);

namespace Quanta;

final class ErrorList implements InputInterface
{
    /**
     * @var \Quanta\ErrorInterface[]
     */
    private $errors;

    /**
     * @param \Quanta\ErrorInterface    $error
     * @param \Quanta\ErrorInterface    ...$errors
     */
    public static function instance(ErrorInterface $error, ErrorInterface ...$errors): self
    {
        return new self($error, ...$errors);
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
     * @inheritdoc
     */
    public function nested(string $key, string ...$keys): self
    {
        return new self(...array_map(fn ($e) => new NestedError($e, $key, ...$keys), $this->errors));
    }

    /**
     * @inheritdoc
     */
    public function map(callable $f): InputInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface ...$inputs): InputInterface
    {
        if (count($inputs) == 0) {
            return $this;
        }

        /** @var \Quanta\InputInterface */
        $input = array_shift($inputs);

        if ($input instanceof FieldInterface) {
            return $this->apply(...$inputs);
        }

        if ($input instanceof ErrorList) {
            return (new self(...$this->errors, ...$input->errors))->apply(...$inputs);
        }

        throw new \InvalidArgumentException(
            sprintf('apply() : the given input must be Quanta\FieldInterface|Quanta\ErrorList, %s given', gettype($input))
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
