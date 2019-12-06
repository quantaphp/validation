<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Success implements ResultInterface
{
    /**
     * @var mixed
     */
    private $x;

    /**
     * @param mixed $x
     */
    public function __construct($x)
    {
        $this->x = $x;
    }

    /**
     * @inheritdoc
     */
    public function input(string $key): InputInterface
    {
        return new Data([$key => $this->x]);
    }

    /**
     * @inheritdoc
     */
    public function bind(callable ...$fs): ResultInterface
    {
        $f = array_shift($fs) ?? false;

        if ($f === false) {
            return $this;
        }

        $result = $f($this->x);

        if ($result instanceof Success) {
            return (new self($result->x))->bind(...$fs);
        }

        if ($result instanceof Failure) {
            return $result->bind(...$fs);
        }

        throw new \InvalidArgumentException(
            sprintf('The given validation must return an instance of Quanta\Validation\Success|Quanta\Validation\Failure, %s returned', gettype($result))
        );
    }

    /**
     * @inheritdoc
     */
    public function extract(callable $success, callable $failure)
    {
        return $success($this->x);
    }
}
