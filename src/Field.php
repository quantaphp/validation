<?php

declare(strict_types=1);

namespace Quanta;

final class Field implements FieldInterface
{
    /**
     * @var callable
     */
    private $f;

    /**
     * @param callable $f
     */
    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    /**
     * @inheritdoc
     */
    public function f(): callable
    {
        return $this->f;
    }

    /**
     * @param \Quanta\InputInterface $input
     * @return \Quanta\Field|\Quanta\NamedField|\Quanta\ErrorList
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Field || $input instanceof NamedField) {
            $f = $input->f();
            $x = ($this->f)();

            return new self(fn (...$xs) => $f($x, ...$xs));
        }

        if ($input instanceof ErrorList) {
            return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\Field|Quanta\NamedField|Quanta\ErrorList, %s given', gettype($input))
        );
    }

    /**
     * @param callable(mixed $value): \Quanta\InputInterface $f
     * @return \Quanta\Field|\Quanta\NamedField|\Quanta\ErrorList
     */
    public function bind(callable $f): InputInterface
    {
        $x = ($this->f)();

        $input = $f($x);

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
        $value = ($this->f)();

        if (is_array($value)) {
            return array_map(function ($key, $value) {
                return NamedField::from((string) $key, new self(fn () => $value));
            }, array_keys($value), $value);
        }

        throw new \LogicException(sprintf('cannot unpack %s', gettype($value)));
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->f()());
    }
}
