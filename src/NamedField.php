<?php

declare(strict_types=1);

namespace Quanta;

final class NamedField implements InputInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Quanta\Field|\Quanta\NamedField|\Quanta\WrappedCallable
     */
    private $field;

    /**
     * @param string                    $name
     * @param \Quanta\InputInterface    $field
     */
    public static function from(string $name, InputInterface $field): self
    {
        if ($field instanceof Field || $field instanceof NamedField || $field instanceof WrappedCallable) {
            return new self($name, $field);
        }

        throw new \InvalidArgumentException(
            sprintf('The given field must be an instance of Quanta\Field|Quanta\NamedField, instance of %s given', get_class($field))
        );
    }

    /**
     * @param string                                                    $name
     * @param \Quanta\Field|\Quanta\NamedField|\Quanta\WrappedCallable  $field
     */
    private function __construct(string $name, $field)
    {
        $this->name = $name;
        $this->field = $field;
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

        if ($input instanceof Field || $input instanceof NamedField || $input instanceof WrappedCallable) {
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
        return array_map(fn ($input) => new self($this->name, $input), $this->field->unpack());
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $this->field->extract($success, $failure);
    }
}
