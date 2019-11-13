<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

final class ArrayKey
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
     * @param array $x
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke(array $x): InputInterface
    {
        $f = new Combined(new HasKey($this->key), new OnKey($this->key), new Named($this->key, ...$this->fs));

        return $f($x);
    }
}
