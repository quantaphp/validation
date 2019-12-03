<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Success implements InputInterface
{
    /**
     * @var array
     */
    private array $xs;

    /**
     * @var string[]
     */
    private array $keys;

    /**
     * @param array     $xs
     * @param string    ...$keys
     */
    public function __construct(array $xs, string ...$keys)
    {
        $this->xs = $xs;
        $this->keys = $keys;
    }

    /**
     * @inheritdoc
     */
    public function map(callable ...$fs): InputInterface
    {
        $f = array_shift($fs) ?? false;

        return $f === false ? $this : (new Success($f($this->xs), ...$this->keys))->map(...$fs);
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

        if ($input instanceof Success) {
            $xs1 = $this->namespaced();
            $xs2 = $input->namespaced();

            return (new Success(array_merge($xs1, $xs2)))->merge(...$inputs);
        }

        if ($input instanceof Failure) {
            return $input;
        }

        throw new \InvalidArgumentException(
            sprintf('The given input must be an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s given', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function bind(callable ...$fs): InputInterface
    {
        $f = array_shift($fs) ?? false;

        if ($f === false) {
            return $this;
        }

        $input = $f($this->xs);

        if ($input instanceof Success) {
            return (new Success($input->xs, ...$this->keys, ...$input->keys))->bind(...$fs);
        }

        if ($input instanceof Failure) {
            return $input->nested(...$this->keys);
        }

        throw new \InvalidArgumentException(
            sprintf('The given validation must return an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s returned', gettype($input))
        );
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->namespaced());
    }

    /**
     * @return array
     */
    private function namespaced(): array
    {
        return $this->prepend($this->xs, ...$this->keys);
    }

    /**
     * @param array     $xs
     * @param string    ...$keys
     * @return array
     */
    private function prepend(array $xs, string ...$keys): array
    {
        $key = array_pop($keys) ?? false;

        return $key === false ? $xs : $this->prepend([$key => $xs], ...$keys);
    }
}
