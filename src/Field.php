<?php

declare(strict_types=1);

namespace Quanta;

final class Field implements InputInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param \Quanta\Field|\Quanta\ErrorList $input
     * @return \Quanta\Field|\Quanta\ErrorList
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Field) {
            $x = $this->value;
            $f = $input->value;

            return new Field(fn (...$xs) => $f($x, ...$xs));
        }

        if ($input instanceof ErrorList) {
            return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\Field|Quanta\ErrorList, %s given', gettype($input))
        );
    }

    /**
     * @param callable(mixed $value): \Quanta\InputInterface $f
     * @return \Quanta\Field|\Quanta\NamedField|\Quanta\ErrorList
     */
    public function bind(callable $f): InputInterface
    {
        $input = $f($this->value);

        if ($input instanceof Field || $input instanceof NamedField || $input instanceof ErrorList) {
            return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('The given callable must return an instance of Quanta\Field|Quanta\NamedField|Quanta\ErrorList, %s returned', gettype($input))
        );
    }

    /**
     * @return \Quanta\NamedField[]
     */
    public function unpack(): array
    {
        if (is_array($this->value)) {
            return array_map(function ($key, $value) {
                return NamedField::from((string) $key, new self($value));
            }, array_keys($this->value), $this->value);
        }

        throw new \LogicException(sprintf('cannot unpack %s', gettype($this->value)));
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->value);
    }
}
