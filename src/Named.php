<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Named
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var array<int, callable(mixed): InputInterface> $fs
     */
    private $fs;

    /**
     * @param string                            $key
     * @param callable(mixed): InputInterface   ...$fs
     */
    public function __construct(string $key, callable ...$fs)
    {
        $this->key = $key;
        $this->fs = $fs;
    }

    /**
     * @param mixed $value
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke($value): InputInterface
    {
        if (count($this->fs) == 0) {
            return Input::unit($value);
        }

        $f = $this->fs[0];
        $fs = array_slice($this->fs, 1);

        $input = $f($value)->bind(...$fs);

        return $input instanceof Failure
            ? $input->nested($this->key)
            : $input;
    }
}
