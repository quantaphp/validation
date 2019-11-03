<?php

declare(strict_types=1);

namespace Quanta;

final class NamedField implements FieldInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Quanta\FieldInterface
     */
    private $field;

    /**
     * @param string                    $name
     * @param \Quanta\FieldInterface    $field
     */
    public function __construct(string $name, FieldInterface $field)
    {
        $this->name = $name;
        $this->field = $field;
    }

    /**
     * @inheritdoc
     */
    public function f(): callable
    {
        return $this->field->f();
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        return $this->field->apply($input);
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $f): InputInterface
    {
        $input = $this->field->bind($f);

        if ($input instanceof Field || $input instanceof NamedField) {
            return new self($this->name, $input);
        }

        if ($input instanceof ErrorList) {
            return $input->named($this->name);
        }
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $this->field->extract($success, $failure);
    }
}
