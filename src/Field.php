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
     * @inheritdoc
     */
    public function map(callable $f): InputInterface
    {
        return (new Field($f))->apply($this);
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

        if ($input instanceof Field || $input instanceof NamedField) {
            $f = $this->f;
            $x = $input->f()();

            return (new self(fn (...$xs) => $f($x, ...$xs)))->apply(...$inputs);
        }

        if ($input instanceof ErrorList) {
            return $input->apply(...$inputs);
        }

        throw new \InvalidArgumentException(
            sprintf('apply() : the given argument must be an instance of Quanta\Field|Quanta\NamedField|Quanta\ErrorList, %s given', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function bind(callable ...$fs): InputInterface
    {
        if (count($fs) == 0) {
            return $this;
        }

        /** @var callable */
        $f = array_shift($fs);
        $x = ($this->f)();

        $input = $f($x);

        if ($input instanceof Field || $input instanceof NamedField) {
            return $input->bind(...$fs);
        }

        if ($input instanceof ErrorList) {
            return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('bind() : the given callable must return an instance of Quanta\FieldInterface|Quanta\ErrorList, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->f()());
    }
}
