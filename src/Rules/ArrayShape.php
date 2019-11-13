<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

final class ArrayShape
{
    /**
     * @var array<string, callable(mixed): \Quanta\Validation\InputInterface>
     */
    private $fs;

    /**
     * @param array<string, callable(mixed): \Quanta\Validation\InputInterface> $fs
     */
    public function __construct(array $fs)
    {
        $this->fs = $fs;
    }

    public function __invoke(array $x): InputInterface
    {
        $keys = array_keys($this->fs);
        $map = fn (string $key, array $fs) => (new ArrayKey($key, ...$fs))($x);
        $combine = fn (...$xs) => array_combine($keys, $xs);

        return Input::map($combine)(...array_map($map, $keys, $this->fs));
    }
}
