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
    public function value()
    {
        return ($this->f)();
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

        if ($input instanceof FieldInterface) {
            $f = $this->f;
            $x = $input->value();

            return (new self(fn (...$xs) => $f($x, ...$xs)))->apply(...$inputs);
        }

        if ($input instanceof ErrorList) {
            return $input->apply(...$inputs);
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
        if (count($fs) == 0) {
            return $this;
        }

        /** @var callable */
        $f = array_shift($fs);

        $input = $f($this->value());

        if ($input instanceof FieldInterface) {
            return $input->bind(...$fs);
        }

        if ($input instanceof ErrorList) {
            return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('bind() : the given callable must return Quanta\FieldInterface|Quanta\ErrorList, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->value());
    }
}
