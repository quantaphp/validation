<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class Named
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var array<int, callable(mixed): \Quanta\Validation\InputInterface> $fs
     */
    private $fs;

    /**
     * @param string                                                $key
     * @param callable(mixed): \Quanta\Validation\InputInterface    ...$fs
     */
    public function __construct(string $key, callable ...$fs)
    {
        $this->key = $key;
        $this->fs = $fs;
    }

    /**
     * @param mixed $x
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke($x): InputInterface
    {
        $f = new Combined(...$this->fs);

        $input = $f($x);

        return $input instanceof Failure
            ? $input->nested($this->key)
            : $input;
    }
}
