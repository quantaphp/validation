<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Nested
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
     * @param array $data
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke(array $data): InputInterface
    {
        $input = Input::unit($data[$this->key])->bind(...$this->fs);

        return $input instanceof Failure
            ? $input->nested($this->key)
            : $input;
    }
}
