<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\ValidationInterface;

final class Success implements InputInterface
{
    private string $name;

    private array $xs;

    public function __construct(string $name, array $xs)
    {
        $this->name = $name;
        $this->xs = $xs;
    }

    public function bind(ValidationInterface ...$fs): InputInterface
    {
        $f = array_shift($fs) ?? false;

        if ($f === false) {
            return $this;
        }

        $input = $f($this->name, $this->xs);

        if ($input instanceof Success || $input instanceof Failure) {
            return $input->bind(...$fs);
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
        return $success($this->xs);
    }
}
