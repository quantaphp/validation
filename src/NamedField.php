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
    public static function from(string $name, FieldInterface $field): self
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
        return $this->field->apply($input);
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
    }

    /**
     * @return \Quanta\NamedField[]
     */
    public function unpack(): array
    {
        $inputs = $this->field->unpack();

        return array_map(fn ($input) => new self($this->name, $input), $inputs);
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $this->field->extract($success, $failure);
    }
}
