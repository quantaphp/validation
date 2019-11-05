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
    private $input;

    /**
     * @param string                    $name
     * @param \Quanta\InputInterface    $input
     */
    public static function from(string $name, InputInterface $input): self
    {
        switch (true) {
            case $input instanceof Field:
            case $input instanceof NamedField:
            case $input instanceof WrappedCallable:
                return new self($name, $input);
        }

        throw new \InvalidArgumentException(
            sprintf('The given input must be an instance of Quanta\Field|Quanta\NamedField|Quanta\WrappedCallable, instance of %s given', get_class($input))
        );
    }

    /**
     * @param string                                                    $name
     * @param \Quanta\Field|\Quanta\NamedField|\Quanta\WrappedCallable  $input
     */
    private function __construct(string $name, $input)
    {
        $this->name = $name;
        $this->input = $input;
    }

    /**
     * @inheritdoc
     */
    public function apply(InputInterface $input): InputInterface
    {
        return $this->input->apply($input);
    }

    /**
     * @inheritdoc
     */
    public function validate(callable ...$fs): InputInterface
    {
        $input = $this->input->validate(...$fs);

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
        return array_map(fn ($input) => new self($this->name, $input), $this->input->unpack(...$fs));
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $this->input->extract($success, $failure);
    }
}
