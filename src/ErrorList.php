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
     * @param \Quanta\Field|\Quanta\ErrorList $input
     * @return \Quanta\ErrorList
     */
    public function apply(InputInterface $input): ErrorList
    {
        if ($input instanceof Field) {
            return $this;
        }

        if ($input instanceof ErrorList) {
            return new self(...$input->errors, ...$this->errors);
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\Field|Quanta\ErrorList, %s given', gettype($input))
        );
    }

    /**
     * @param callable(mixed $value): \Quanta\InputInterface $f
     * @return \Quanta\ErrorList
     */
    public function bind(callable $f): InputInterface
    {
        return $this;
    }

    /**
     * @return \Quanta\ErrorList[]
     */
    public function unpack(): array
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
