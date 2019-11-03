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
    public function __construct(ErrorInterface $error, ErrorInterface ...$errors)
    {
        $this->errors = [$error, ...$errors];
    }

    /**
     * @param string $name
     * @return \Quanta\ErrorList
     */
    public function named(string $name): self
    {
        return new self(...array_map(fn ($e) => new NamedError($name, $e), $this->errors));
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Field || $input instanceof NamedField) {
            return $this;
        }

        if ($input instanceof ErrorList) {
            return new self(...$this->errors, ...$input->errors);
        }

        throw new \InvalidArgumentException(
            sprintf('apply() : the given input must be Quanta\Field|Quanta\NamedField|Quanta\ErrorList, %s given', gettype($input))
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
    public function extract(callable $success, callable $failure)
    {
        return $failure(...$this->errors);
    }
}
