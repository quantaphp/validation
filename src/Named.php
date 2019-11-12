<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Named
{
    /**
     * @var array<int, callable(mixed): InputInterface> $fs
     */
    private $fs;

    /**
     * @param callable(mixed): InputInterface ...$fs
     */
    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param mixed $value
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke(string $key, $value): InputInterface
    {
        $input = Input::unit($value)->bind(...$this->fs);

        return $input instanceof Failure
            ? $input->nested($key)
            : $input;
    }
}
