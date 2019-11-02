<?php

declare(strict_types=1);

namespace Quanta;

final class NamedField implements FieldInterface
{
    /**
     * @var callable
     */
    private $f;

    /**
     * @var string[]
     */
    private $keys;

    /**
     * @param callable  $f
     * @param string    $key
     * @param string    ...$keys
     */
    public function __construct(callable $f, string $key, string ...$keys)
    {
        $this->keys = [$key, ...$keys];
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

            return (new self(fn (...$xs) => $f($x, ...$xs), ...$this->keys))->apply(...$inputs);
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

        $input = $f($this->f()());

        if ($input instanceof Field) {
            return (new self($input->f(), ...$this->keys))->bind(...$fs);
        }

        if ($input instanceof NamedField) {
            return (new self($input->f(), ...$this->keys, ...$input->keys))->bind(...$fs);
        }

        if ($input instanceof ErrorList) {
            return $input->nested(...$this->keys)->bind(...$fs);
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
