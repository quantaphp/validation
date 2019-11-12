<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Named;
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
        $keys = array_keys($data);
        $values = array_map(fn ($k, $v) => [(string) $k, $v], $keys, $this->shape);

        $map = function (array $tuple) use ($data) {
            [$key, $fs] = $tuple;

            return (new HasKey($key))($data)->bindkey($key, ...$fs);
        };

        $combine = fn (...$xs) => array_combine($keys, $xs);

        return Input::map($combine)(...array_map($map, $values));
    }
}
