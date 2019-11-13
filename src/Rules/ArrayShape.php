<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

final class ArrayShape
{
    private $shape;

    public function __construct(array $shape)
    {
        $this->shape = $shape;
    }

    public function __invoke(array $data): InputInterface
    {
        $keys = array_keys($this->shape);
        $map = fn (string $key, array $fs) => (new HasKey($key))($data)->bind(fn ($x) => (new Named($key, ...$fs))($x[$key]));
        $combine = fn (...$xs) => array_combine($keys, $xs);

        return Input::map($combine)(...array_map($map, $keys, $this->shape));
    }
}
