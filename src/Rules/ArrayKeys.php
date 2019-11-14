<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

final class ArrayKeys
{
    /**
     * @var array<string, array<int, callable(mixed): \Quanta\Validation\InputInterface>>
     */
    private $fs;

    /**
     * @param array<string, array<int, callable(mixed): \Quanta\Validation\InputInterface>> $fs
     */
    public function __construct(array $fs)
    {
        $this->fs = $fs;
    }

    public function __invoke(array $x): InputInterface
    {
        $keys = array_keys($this->fs);
        $map = fn (string $key, array $fs) => (new ArrayKey($key, ...$fs))($x);

        $combine = Input::map(fn (...$xs) => array_combine($keys, $xs));

        return $combine(...array_map($map, $keys, $this->fs));
    }
}
