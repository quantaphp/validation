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
    public function validate(callable ...$fs): InputInterface
    {
        $input = $this->field->validate(...$fs);

        switch (true) {
            case $input instanceof Field:
            case $input instanceof NamedField:
            case $input instanceof WrappedCallable:
                return new self($this->name, $input);
            case $input instanceof ErrorList:
                return $input->named($this->name);
        }
    }

    /**
     * @inheritdoc
     */
    public function unpack(callable ...$fs): array
    {
        return array_map(fn ($input) => new self($this->name, $input), $this->field->unpack(...$fs));
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $this->field->extract($success, $failure);
    }
}
