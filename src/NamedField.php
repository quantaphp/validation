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
     * @var \Quanta\Field|\Quanta\NamedField
     */
    private $field;

    /**
     * @param string                    $name
     * @param \Quanta\FieldInterface    $field
     */
    public function from(string $name, FieldInterface $field): self
    {
        if ($field instanceof Field || $field instanceof NamedField) {
            return new self($name, $field);
        }

        throw new \InvalidArgumentException(
            sprintf('The given field must be an instance of Quanta\Field|Quanta\NamedField, instance of %s given', get_class($field))
        );
    }

    /**
     * @param string                            $name
     * @param \Quanta\Field|\Quanta\NamedField  $field
     */
    private function __construct(string $name, FieldInterface $field)
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
     * @param \Quanta\InputInterface $input
     * @return \Quanta\Field|\Quanta\NamedField|\Quanta\ErrorList
     */
    public function apply(InputInterface $input): InputInterface
    {
        if ($input instanceof Field || $input instanceof NamedField || $input instanceof ErrorList) {
            return $this->field->apply($input);
        }

        throw new \InvalidArgumentException(
            sprintf('The given argument must be an instance of Quanta\Field|Quanta\NamedField|Quanta\ErrorList, instance of %s given', get_class($input))
        );
    }

    /**
     * @param callable(mixed $value): \Quanta\InputInterface $f
     * @return \Quanta\Field|\Quanta\NamedField|\Quanta\ErrorList
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

        throw new \InvalidArgumentException(
            sprintf('The given callable must return an instance of Quanta\Field|Quanta\NamedField|Quanta\ErrorList, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $this->field->extract($success, $failure);
    }
}
