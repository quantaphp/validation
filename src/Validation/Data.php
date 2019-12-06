<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Data implements InputInterface
{
    /**
     * @var mixed[]
     */
    private array $xs;

    /**
     * @param mixed[] $xs
     */
    public function __construct(array $xs)
    {
        $this->xs = $xs;
    }

    /**
     * @inheritdoc
     */
    public function result(): ResultInterface
    {
        return new Success($this->xs);
    }

    /**
     * @inheritdoc
     */
    public function merge(InputInterface ...$inputs): InputInterface
    {
        $input = array_shift($inputs) ?? false;

        if ($input == false) {
            return $this;
        }

        if ($input instanceof Data) {
            return (new self(array_merge($this->xs, $input->xs)))->merge(...$inputs);
        }

        if ($input instanceof Failure) {
            return $input->merge(...$inputs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given input must be an instance of Quanta\Validation\Data|Quanta\Validation\Failure, %s given', gettype($input))
        );
    }
}
